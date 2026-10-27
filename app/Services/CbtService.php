<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;
use App\Models\StudentAnswer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CbtService
{
    /**
     * Durasi rotasi token dalam detik (5 Menit = 300 detik)
     */
    public const TOKEN_INTERVAL_SECONDS = 300;

    /**
     * Karakter yang digunakan untuk Token CBT (mudah dibaca, bebas ambiguitas 0/O, 1/I)
     */
    protected const TOKEN_CHARS = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    /**
     * Dapatkan token aktif untuk Quiz tertentu (atau global).
     * Token berganti setiap 5 menit (300 detik).
     */
    public function getTokenInfo(int|Quiz|null $quiz = null): array
    {
        $quizId = $quiz instanceof Quiz ? $quiz->id : ($quiz ?? 0);
        $window = (int) floor(time() / self::TOKEN_INTERVAL_SECONDS);
        $elapsed = time() % self::TOKEN_INTERVAL_SECONDS;
        $remaining = self::TOKEN_INTERVAL_SECONDS - $elapsed;

        $salt = Cache::get("cbt_token_salt_{$quizId}_{$window}", 'CBT_ASSYAFIIYAH_DEMO');
        $token = $this->hashToToken("{$quizId}_{$window}_{$salt}");

        return [
            'token' => $token,
            'quiz_id' => $quizId,
            'window' => $window,
            'remaining_seconds' => $remaining,
            'interval_seconds' => self::TOKEN_INTERVAL_SECONDS,
            'expires_at' => now()->addSeconds($remaining)->toIso8601String(),
        ];
    }

    /**
     * Regenerate token secara manual untuk quiz saat ini
     */
    public function forceRegenerateToken(int|Quiz|null $quiz = null): array
    {
        $quizId = $quiz instanceof Quiz ? $quiz->id : ($quiz ?? 0);
        $window = (int) floor(time() / self::TOKEN_INTERVAL_SECONDS);
        $newSalt = Str::random(12);

        // Simpan salt baru untuk window saat ini selama 10 menit
        Cache::put("cbt_token_salt_{$quizId}_{$window}", $newSalt, 600);

        return $this->getTokenInfo($quizId);
    }

    /**
     * Validasi token ujian dari siswa
     */
    public function validateToken(string $inputToken, int|Quiz|null $quiz = null): bool
    {
        $cleanInput = strtoupper(trim(str_replace([' ', '-'], '', $inputToken)));
        if (empty($cleanInput)) {
            return false;
        }

        $quizId = $quiz instanceof Quiz ? $quiz->id : ($quiz ?? 0);
        $currentWindow = (int) floor(time() / self::TOKEN_INTERVAL_SECONDS);

        // Periksa window saat ini dan 1 window sebelumnya (toleransi peralihan 5 menit)
        $windowsToCheck = [$currentWindow, $currentWindow - 1];

        foreach ($windowsToCheck as $w) {
            $salt = Cache::get("cbt_token_salt_{$quizId}_{$w}", 'CBT_ASSYAFIIYAH_DEMO');
            $expectedToken = $this->hashToToken("{$quizId}_{$w}_{$salt}");

            if ($cleanInput === str_replace('-', '', $expectedToken)) {
                return true;
            }

            // Periksa juga jika ada token global (quizId = 0)
            if ($quizId !== 0) {
                $globalSalt = Cache::get("cbt_token_salt_0_{$w}", 'CBT_ASSYAFIIYAH_DEMO');
                $expectedGlobalToken = $this->hashToToken("0_{$w}_{$globalSalt}");
                if ($cleanInput === str_replace('-', '', $expectedGlobalToken)) {
                    return true;
                }
            }
        }

        // Fitur Master Demo Token untuk testing jika diperlukan
        if ($cleanInput === 'DEMO99' || $cleanInput === 'CBT123') {
            return true;
        }

        return false;
    }

    /**
     * Hash string menjadi token 6 karakter uppercase format: ABC-DEF atau ABC123
     */
    protected function hashToToken(string $payload): string
    {
        $hash = hash_hmac('sha256', $payload, config('app.key', 'lms_secret_key_demo'));
        $token = '';
        $charsLen = strlen(self::TOKEN_CHARS);

        for ($i = 0; $i < 6; $i++) {
            $hexPart = substr($hash, $i * 4, 4);
            $num = hexdec($hexPart);
            $token .= self::TOKEN_CHARS[$num % $charsLen];
        }

        return substr($token, 0, 3) . '-' . substr($token, 3, 3);
    }

    /**
     * Mulai sesi pengerjaan ujian untuk siswa
     */
    public function startAttempt(Quiz $quiz, Student $student, string $token): QuizAttempt
    {
        if (!$this->validateToken($token, $quiz)) {
            throw new \InvalidArgumentException('Token ujian tidak valid atau sudah kedaluwarsa. Silakan periksa token 5-menit terbaru dari pengawas ujian.');
        }

        if (!$quiz->isActive()) {
            throw new \InvalidArgumentException('Ujian belum dimulai atau jadwal pengerjaan telah berakhir.');
        }

        // Periksa apakah attempt sudah ada
        $existingAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->isCompleted()) {
                throw new \InvalidArgumentException('Anda sudah menyelesaikan ujian ini dan tidak dapat mengulanginya lagi.');
            }
            return $existingAttempt;
        }

        $now = now();
        $expiresAt = $now->copy()->addMinutes($quiz->duration);

        // Jika ujian memiliki end_time yang lebih cepat dari durasi, batasi expires_at
        if ($quiz->end_time && $quiz->end_time->lt($expiresAt)) {
            $expiresAt = $quiz->end_time;
        }

        return QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'start_time' => $now,
            'expires_at' => $expiresAt,
            'status' => 'ongoing',
            'score' => 0,
            'total_points' => $quiz->total_points,
            'earned_points' => 0,
            'needs_grading' => false,
        ]);
    }

    /**
     * Simpan jawaban siswa secara instan (Auto-save)
     */
    public function saveAnswer(QuizAttempt $attempt, int $questionId, ?int $optionId = null, ?string $essay = null, ?array $matchingAnswer = null): StudentAnswer
    {
        if (!$attempt->isOngoing()) {
            throw new \RuntimeException('Sesi ujian telah berakhir.');
        }

        $question = Question::with('options')->findOrFail($questionId);
        $isCorrect = null;
        $score = null;

        if ($question->type === 'multiple_choice' && $optionId) {
            $selectedOption = $question->options->firstWhere('id', $optionId);
            $isCorrect = $selectedOption ? (bool) $selectedOption->is_correct : false;
            $score = $isCorrect ? (float) $question->points : 0.0;
        } elseif ($question->type === 'complex_true_false' && is_array($matchingAnswer) && $question->options->isNotEmpty()) {
            $totalStatements = $question->options->count();
            $correctCount = 0;
            foreach ($question->options as $opt) {
                if (isset($matchingAnswer[$opt->id])) {
                    $studentChoice = filter_var($matchingAnswer[$opt->id], FILTER_VALIDATE_BOOLEAN);
                    if ($studentChoice === (bool) $opt->is_correct) {
                        $correctCount++;
                    }
                }
            }
            $isCorrect = ($correctCount === $totalStatements);
            $score = round(($correctCount / $totalStatements) * (float) $question->points, 2);
        }

        return StudentAnswer::updateOrCreate(
            [
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'question_option_id' => $optionId,
                'essay_answer' => $essay,
                'matching_answer' => $matchingAnswer,
                'is_correct' => $isCorrect,
                'score' => $score,
            ]
        );
    }

    /**
     * Selesaikan ujian dan kalkulasi nilai akhir
     */
    public function finishAttempt(QuizAttempt $attempt): QuizAttempt
    {
        if ($attempt->isCompleted()) {
            return $attempt;
        }

        $quiz = $attempt->quiz()->with('questions.options')->first();
        $answers = $attempt->answers()->with('question.options')->get();

        $totalPoints = 0.0;
        $earnedPoints = 0.0;
        $hasEssay = false;

        foreach ($quiz->questions as $question) {
            $totalPoints += (float) $question->points;
            $answer = $answers->firstWhere('question_id', $question->id);

            if ($question->type === 'essay') {
                $hasEssay = true;
            } elseif ($question->type === 'multiple_choice' && $answer && $answer->question_option_id) {
                $correctOpt = $question->options->firstWhere('is_correct', true);
                if ($correctOpt && $answer->question_option_id == $correctOpt->id) {
                    $earnedPoints += (float) $question->points;
                    $answer->update([
                        'is_correct' => true,
                        'score' => (float) $question->points,
                    ]);
                } else {
                    $answer->update([
                        'is_correct' => false,
                        'score' => 0.0,
                    ]);
                }
            } elseif ($question->type === 'complex_true_false' && $answer && is_array($answer->matching_answer) && $question->options->isNotEmpty()) {
                $totalStatements = $question->options->count();
                $correctCount = 0;
                foreach ($question->options as $opt) {
                    if (isset($answer->matching_answer[$opt->id])) {
                        $studentChoice = filter_var($answer->matching_answer[$opt->id], FILTER_VALIDATE_BOOLEAN);
                        if ($studentChoice === (bool) $opt->is_correct) {
                            $correctCount++;
                        }
                    }
                }
                $isAllCorrect = ($correctCount === $totalStatements);
                $pts = round(($correctCount / $totalStatements) * (float) $question->points, 2);
                $earnedPoints += $pts;
                $answer->update([
                    'is_correct' => $isAllCorrect,
                    'score' => $pts,
                ]);
            }
        }

        $finalScore = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;

        $attempt->update([
            'end_time' => now(),
            'status' => 'completed',
            'score' => $finalScore,
            'total_points' => $totalPoints,
            'earned_points' => $earnedPoints,
            'needs_grading' => $hasEssay,
        ]);

        return $attempt->fresh();
    }

    /**
     * Import Soal dari file Excel / CSV / TXT / DOC
     */
    public function importQuestionsFromFile(Quiz $quiz, UploadedFile $file, ?int $userId = null): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
            $results = $this->importFromSpreadsheet($quiz, $file, $userId);
        } else {
            $results = $this->importFromTextOrDoc($quiz, $file, $userId);
        }

        return $results;
    }

    /**
     * Import dari file Spreadsheet (XLSX, XLS, CSV)
     */
    protected function importFromSpreadsheet(Quiz $quiz, UploadedFile $file, ?int $userId = null): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        try {
            // Gunakan PhpSpreadsheet / fgetcsv jika extension csv
            if ($file->getClientOriginalExtension() === 'csv') {
                $handle = fopen($file->getRealPath(), 'r');
                $rows = [];
                while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            } else {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
            }

            if (empty($rows)) {
                return ['success' => 0, 'failed' => 0, 'errors' => ['File spreadsheet kosong atau tidak terbaca.']];
            }

            // Lewati header baris 1 jika ada kata "soal", "pertanyaan", atau "no"
            $firstCol = strtolower(trim($rows[0][0] ?? ''));
            $startIndex = ($firstCol === 'no' || str_contains($firstCol, 'soal')) ? 1 : 0;

            $order = $quiz->questions()->max('order_number') ?? 0;

            DB::beginTransaction();

            for ($i = $startIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                // Format kolom: [0: No, 1: Pertanyaan, 2: Tipe, 3: Opsi A, 4: Opsi B, 5: Opsi C, 6: Opsi D, 7: Opsi E, 8: Kunci Jawaban, 9: Bobot]
                $questionText = trim($row[1] ?? $row[0] ?? '');
                if (empty($questionText)) {
                    continue;
                }

                $order++;
                $type = (isset($row[2]) && strtolower(trim($row[2])) === 'essay') ? 'essay' : 'multiple_choice';
                $points = isset($row[9]) && is_numeric($row[9]) ? (int) $row[9] : 1;
                $keyAnswer = strtoupper(trim($row[8] ?? 'A'));

                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'created_by' => $userId,
                    'question_text' => $questionText,
                    'type' => $type,
                    'difficulty' => 'medium',
                    'points' => max(1, $points),
                    'order_number' => $order,
                    'is_active' => true,
                ]);

                if ($type === 'multiple_choice') {
                    $options = [
                        'A' => $row[3] ?? '',
                        'B' => $row[4] ?? '',
                        'C' => $row[5] ?? '',
                        'D' => $row[6] ?? '',
                        'E' => $row[7] ?? '',
                    ];

                    $optIndex = 0;
                    foreach ($options as $key => $optText) {
                        $optText = trim((string) $optText);
                        if ($optText !== '') {
                            QuestionOption::create([
                                'question_id' => $question->id,
                                'option_text' => $optText,
                                'is_correct' => ($key === $keyAnswer),
                                'order_number' => ++$optIndex,
                            ]);
                        }
                    }
                }

                $results['success']++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CBT Import Spreadsheet Error: ' . $e->getMessage());
            $results['errors'][] = 'Terjadi kesalahan saat mengimpor Excel: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Import dari format teks Word/Docs (format terstruktur Aiken / GIFT / Markdown)
     */
    protected function importFromTextOrDoc(Quiz $quiz, UploadedFile $file, ?int $userId = null): array
    {
        $content = file_get_contents($file->getRealPath());
        // Normalisasi line breaks
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $blocks = preg_split('/\n\s*\n/', trim($content));

        $results = ['success' => 0, 'failed' => 0, 'errors' => []];
        $order = $quiz->questions()->max('order_number') ?? 0;

        DB::beginTransaction();

        try {
            foreach ($blocks as $block) {
                $lines = array_map('trim', explode("\n", trim($block)));
                if (empty($lines)) {
                    continue;
                }

                $questionText = '';
                $options = [];
                $correctKey = 'A';
                $points = 1;
                $isEssay = false;

                foreach ($lines as $line) {
                    if (preg_match('/^(?:KUNCI|ANSWER|JAWABAN):\s*([A-E])/i', $line, $matches)) {
                        $correctKey = strtoupper($matches[1]);
                    } elseif (preg_match('/^(?:BOBOT|POIN|POINTS):\s*(\d+)/i', $line, $matches)) {
                        $points = (int) $matches[1];
                    } elseif (preg_match('/^(?:TIPE|TYPE):\s*(ESSAY|ESAI)/i', $line)) {
                        $isEssay = true;
                    } elseif (preg_match('/^([A-E])[\.\)]\s*(.*)$/i', $line, $matches)) {
                        $options[strtoupper($matches[1])] = trim($matches[2]);
                    } else {
                        // Hilangkan nomor di awal soal jika ada misal "1. ", "2) "
                        $cleanLine = preg_replace('/^\d+[\.\)]\s*/', '', $line);
                        $questionText .= ($questionText ? "\n" : '') . $cleanLine;
                    }
                }

                if (empty($questionText)) {
                    continue;
                }

                $order++;
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'created_by' => $userId,
                    'question_text' => $questionText,
                    'type' => $isEssay ? 'essay' : 'multiple_choice',
                    'difficulty' => 'medium',
                    'points' => max(1, $points),
                    'order_number' => $order,
                    'is_active' => true,
                ]);

                if (!$isEssay && !empty($options)) {
                    $optIndex = 0;
                    foreach ($options as $key => $optText) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optText,
                            'is_correct' => ($key === $correctKey),
                            'order_number' => ++$optIndex,
                        ]);
                    }
                }

                $results['success']++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CBT Import Text Error: ' . $e->getMessage());
            $results['errors'][] = 'Terjadi kesalahan saat mengimpor soal teks: ' . $e->getMessage();
        }

        return $results;
    }
}
