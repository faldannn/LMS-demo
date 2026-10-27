<?php

namespace App\Http\Controllers;

use App\Models\ClassSubject;
use App\Models\Classes;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Subject;
use App\Services\CbtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CbtController extends Controller
{
    public function __construct(
        protected CbtService $cbtService
    ) {}

    /**
     * Dashboard CBT untuk Admin dan Guru
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Quiz::with(['classSubject.class', 'classSubject.subject', 'classSubject.teacher.user'])
            ->withCount(['questions', 'attempts'])
            ->latest();

        // Jika Guru, hanya tampilkan ujian untuk mata pelajaran yang diampunya
        if ($user->isGuru()) {
            $teacher = $user->teacher;
            if ($teacher) {
                $query->whereHas('classSubject', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                });
            }
        }

        if ($request->filled('class_id')) {
            $query->whereHas('classSubject', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $quizzes = $query->paginate(15)->withQueryString();
        $classes = Classes::orderBy('name')->get();

        // Token Ujian 5-Menitan untuk sesi aktif / global
        $tokenInfo = $this->cbtService->getTokenInfo(0);

        return view('cbt.index', compact('quizzes', 'classes', 'tokenInfo'));
    }

    /**
     * Endpoint Polling Token 5-Menit Real-time
     */
    public function tokenPoll(Request $request): JsonResponse
    {
        $quizId = $request->query('quiz_id');
        $tokenInfo = $this->cbtService->getTokenInfo($quizId ? (int) $quizId : 0);

        return response()->json([
            'success' => true,
            'data' => $tokenInfo,
        ]);
    }

    /**
     * Force Refresh Token oleh Admin/Pengawas
     */
    public function forceRefreshToken(Request $request): JsonResponse
    {
        $quizId = $request->input('quiz_id', 0);
        $tokenInfo = $this->cbtService->forceRegenerateToken((int) $quizId);

        return response()->json([
            'success' => true,
            'message' => 'Token ujian 5-menit berhasil diperbarui.',
            'data' => $tokenInfo,
        ]);
    }

    /**
     * Form Buat Ujian CBT Baru
     */
    public function create(): View
    {
        $user = Auth::user();
        $classSubjectsQuery = ClassSubject::with(['class', 'subject', 'teacher.user'])->orderBy('class_id');

        if ($user->isGuru() && $user->teacher) {
            $classSubjectsQuery->where('teacher_id', $user->teacher->id);
        }

        $classSubjects = $classSubjectsQuery->get();

        return view('cbt.create', compact('classSubjects'));
    }

    /**
     * Simpan Ujian CBT Baru
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'class_subject_id' => 'required|exists:class_subject,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:5|max:300',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'pass_score' => 'required|numeric|min:0|max:100',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
            'show_results' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $quiz = Quiz::create([
            'class_subject_id' => $request->class_subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'pass_score' => $request->pass_score,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_answers' => $request->boolean('shuffle_answers'),
            'show_results' => $request->boolean('show_results'),
            'is_published' => $request->boolean('is_published'),
            'selection_mode' => 'fixed',
            'questions_per_page' => 1,
            'auto_submit_on_timeout' => true,
        ]);

        return redirect()->route('cbt.show', $quiz->id)
            ->with('success', 'Ujian CBT "' . $quiz->title . '" berhasil dibuat! Silakan tambahkan soal atau import file template.');
    }

    /**
     * Detail Ujian & Kelola Soal (Preview KaTeX, Teks Arab, Quick Math Bar)
     */
    public function show(int $id): View
    {
        $quiz = Quiz::with([
            'classSubject.class',
            'classSubject.subject',
            'classSubject.teacher.user',
            'questions.options',
            'attempts.student.user',
        ])->findOrFail($id);

        $tokenInfo = $this->cbtService->getTokenInfo($quiz);

        return view('cbt.show', compact('quiz', 'tokenInfo'));
    }

    /**
     * Upload / Proses file gambar soal dengan proteksi storage serverless
     */
    protected function handleQuestionImage(Request $request, ?string $currentImage = null): ?string
    {
        if ($request->boolean('remove_image')) {
            if ($currentImage && !str_starts_with($currentImage, 'data:') && !str_starts_with($currentImage, 'http')) {
                @unlink(public_path($currentImage));
            }
            return null;
        }

        if (!$request->hasFile('image')) {
            return $currentImage;
        }

        $file = $request->file('image');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = 'q_' . time() . '_' . Str::random(8) . '.' . $ext;

        $dir = public_path('uploads/questions');
        try {
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
            $file->move($dir, $filename);

            if ($currentImage && !str_starts_with($currentImage, 'data:') && !str_starts_with($currentImage, 'http')) {
                @unlink(public_path($currentImage));
            }

            return 'uploads/questions/' . $filename;
        } catch (\Throwable $e) {
            // Serverless fallback: convert to base64
            $mime = $file->getMimeType() ?: 'image/jpeg';
            $data = base64_encode(file_get_contents($file->getRealPath()));
            return "data:{$mime};base64,{$data}";
        }
    }

    /**
     * Simpan 1 Soal Manual (Mendukung KaTeX Math, Arab, Gambar, dan Pilihan Ganda Kompleks)
     */
    public function storeQuestion(Request $request, int $quizId): RedirectResponse
    {
        $quiz = Quiz::findOrFail($quizId);

        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,complex_true_false,essay',
            'points' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'options' => 'nullable|array',
            'correct_option' => 'nullable|string',
            'statements' => 'nullable|array',
            'statement_answers' => 'nullable|array',
        ]);

        $imagePath = $this->handleQuestionImage($request);
        $order = ($quiz->questions()->max('order_number') ?? 0) + 1;

        DB::beginTransaction();
        try {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'created_by' => Auth::id(),
                'question_text' => $request->question_text,
                'image_path' => $imagePath,
                'type' => $request->type,
                'difficulty' => $request->input('difficulty', 'medium'),
                'points' => $request->points,
                'order_number' => $order,
                'is_active' => true,
            ]);

            if ($request->type === 'multiple_choice' && $request->has('options')) {
                $correctKey = $request->input('correct_option', 'A');
                $orderNum = 0;

                foreach ($request->options as $key => $optText) {
                    $optText = trim((string) $optText);
                    if ($optText !== '') {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optText,
                            'is_correct' => ($key === $correctKey),
                            'order_number' => ++$orderNum,
                        ]);
                    }
                }
            } elseif ($request->type === 'complex_true_false' && $request->has('statements')) {
                $statementAnswers = $request->input('statement_answers', []);
                $orderNum = 0;

                foreach ($request->statements as $idx => $statementText) {
                    $statementText = trim((string) $statementText);
                    if ($statementText !== '') {
                        $isTrue = isset($statementAnswers[$idx]) && ($statementAnswers[$idx] === 'true' || $statementAnswers[$idx] === '1');
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $statementText,
                            'is_correct' => $isTrue,
                            'order_number' => ++$orderNum,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('cbt.show', $quiz->id)
                ->with('success', 'Soal berhasil ditambahkan ke ujian.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan soal: ' . $e->getMessage());
        }
    }

    /**
     * Update Butir Soal (Teks, Gambar, Tipe, Opsi / Pernyataan Benar-Salah)
     */
    public function updateQuestion(Request $request, int $quizId, int $questionId): RedirectResponse
    {
        $quiz = Quiz::findOrFail($quizId);
        $question = Question::where('quiz_id', $quiz->id)->findOrFail($questionId);

        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,complex_true_false,essay',
            'points' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'options' => 'nullable|array',
            'correct_option' => 'nullable|string',
            'statements' => 'nullable|array',
            'statement_answers' => 'nullable|array',
        ]);

        $imagePath = $this->handleQuestionImage($request, $question->image_path);

        DB::beginTransaction();
        try {
            $question->update([
                'question_text' => $request->question_text,
                'image_path' => $imagePath,
                'type' => $request->type,
                'difficulty' => $request->input('difficulty', $question->difficulty ?: 'medium'),
                'points' => $request->points,
            ]);

            // Re-sync options based on type
            $question->options()->delete();

            if ($request->type === 'multiple_choice' && $request->has('options')) {
                $correctKey = $request->input('correct_option', 'A');
                $orderNum = 0;

                foreach ($request->options as $key => $optText) {
                    $optText = trim((string) $optText);
                    if ($optText !== '') {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optText,
                            'is_correct' => ($key === $correctKey),
                            'order_number' => ++$orderNum,
                        ]);
                    }
                }
            } elseif ($request->type === 'complex_true_false' && $request->has('statements')) {
                $statementAnswers = $request->input('statement_answers', []);
                $orderNum = 0;

                foreach ($request->statements as $idx => $statementText) {
                    $statementText = trim((string) $statementText);
                    if ($statementText !== '') {
                        $isTrue = isset($statementAnswers[$idx]) && ($statementAnswers[$idx] === 'true' || $statementAnswers[$idx] === '1');
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $statementText,
                            'is_correct' => $isTrue,
                            'order_number' => ++$orderNum,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('cbt.show', $quiz->id)
                ->with('success', 'Butir soal berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui soal: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Soal dari Ujian beserta file gambarnya jika ada
     */
    public function destroyQuestion(int $quizId, int $questionId): RedirectResponse
    {
        $quiz = Quiz::findOrFail($quizId);
        $question = Question::where('quiz_id', $quiz->id)->findOrFail($questionId);

        if ($question->image_path && !str_starts_with($question->image_path, 'data:') && !str_starts_with($question->image_path, 'http')) {
            @unlink(public_path($question->image_path));
        }

        $question->delete();

        return redirect()->route('cbt.show', $quiz->id)
            ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Import Soal CBT dari File Excel / Word / Docs
     */
    public function importQuestions(Request $request, int $quizId): RedirectResponse
    {
        $quiz = Quiz::findOrFail($quizId);

        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $result = $this->cbtService->importQuestionsFromFile($quiz, $file, Auth::id());

        if (!empty($result['errors'])) {
            return redirect()->route('cbt.show', $quiz->id)
                ->with('warning', "Berhasil mengimpor {$result['success']} soal, tetapi terdapat catatan: " . implode('; ', $result['errors']));
        }

        return redirect()->route('cbt.show', $quiz->id)
            ->with('success', "Alhamdulillah! Sebanyak {$result['success']} butir soal berhasil diimpor.");
    }

    /**
     * Download Template Pembuatan Soal (Excel & Word Format)
     */
    public function downloadTemplate(string $format = 'excel'): Response
    {
        if ($format === 'word' || $format === 'docs') {
            return $this->downloadWordTemplate();
        }

        return $this->downloadExcelTemplate();
    }

    /**
     * Generate Template Excel CSV yang kompatibel 100% dengan Excel & PhpSpreadsheet
     */
    protected function downloadExcelTemplate(): Response
    {
        $filename = 'Template_Soal_CBT_LMS_Sekolah_Digital.csv';

        $headers = [
            'No',
            'Pertanyaan (Bisa Rumus LaTeX $...$ atau Teks Arab)',
            'Tipe (multiple_choice/essay)',
            'Pilihan A',
            'Pilihan B',
            'Pilihan C',
            'Pilihan D',
            'Pilihan E',
            'Kunci Jawaban (A/B/C/D/E)',
            'Bobot Nilai',
        ];

        $sampleRows = [
            [
                1,
                'Nilai dari limit $\lim_{x \to 0} \frac{\sin 2x}{x}$ adalah ...',
                'multiple_choice',
                '0',
                '1',
                '2',
                '1/2',
                '$\infty$',
                'C',
                2,
            ],
            [
                2,
                'Reaksi pembentukan air dinyatakan sebagai: $2\text{H}_2 + \text{O}_2 \rightarrow 2\text{H}_2\text{O}$. Jumlah molekul hidrogen yang bereaksi adalah ...',
                'multiple_choice',
                '1 molekul',
                '2 molekul',
                '3 molekul',
                '4 molekul',
                '5 molekul',
                'B',
                2,
            ],
            [
                3,
                'Sebuah benda bergerak lurus dengan persamaan $s(t) = 3t^2 + 4t + 5$. Kecepatan benda pada saat $t = 2$ detik adalah ...',
                'multiple_choice',
                '10 m/s',
                '12 m/s',
                '16 m/s',
                '20 m/s',
                '24 m/s',
                'C',
                2,
            ],
            [
                4,
                'Arti dari kalimat bahasa Arab berikut: مَنْ جَدَّ وَجَدَ adalah ...',
                'multiple_choice',
                'Barangsiapa bersabar maka beruntunglah ia',
                'Barangsiapa bersungguh-sungguh maka ia akan berhasil',
                'Kebersihan adalah sebagian dari iman',
                'Menuntut ilmu itu wajib bagi setiap muslim',
                'Waktu itu ibarat pedang',
                'B',
                2,
            ],
            [
                5,
                'Jelaskan konsep Hukum II Newton dan berikan contoh penerapannya dalam kehidupan sehari-hari!',
                'essay',
                '',
                '',
                '',
                '',
                '',
                '',
                5,
            ],
        ];

        $output = fopen('php://memory', 'r+');
        // UTF-8 BOM agar Excel menampilkan huruf Arab & simbol KaTeX dengan benar
        fputs($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers);

        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Download Panduan Template Docs / Word Terstruktur
     */
    protected function downloadWordTemplate(): Response
    {
        $filename = 'Panduan_Template_Soal_CBT_Word.txt';

        $content = <<<TEXT
================================================================================
   TEMPLATE & PANDUAN PEMBUATAN SOAL CBT (WORD / DOCS) - SEKOLAH DIGITAL
================================================================================

PETUNJUK PENGISIAN SOAL:
1. Pisahkan setiap butir soal dengan 1 baris kosong (Enter 2x).
2. Tuliskan opsi A, B, C, D, E diawali huruf opsi dan titik (Contoh: A. Jawaban).
3. Tuliskan KUNCI: [A/B/C/D/E] di bawah pilihan jawaban.
4. Tuliskan BOBOT: [angka] (Opsional, default 1).
5. Untuk soal Essay, tambahkan baris TIPE: ESSAY.

DUKUNGAN RUMUS LENGKAP:
- Matematika / Fisika: Gunakan tanda dollar ($) untuk LaTeX.
  Contoh: $\$E = mc^2\$, \$\frac{-b \pm \sqrt{b^2 - 4ac}}{2a}\$, \$\int_0^\pi \sin(x)dx\$
- Kimia: \$\text{H}_2\text{SO}_4\$, \$2\text{H}_2 + \text{O}_2 \rightarrow 2\text{H}_2\text{O}\$
- Bahasa Arab: Langsung ketik atau paste teks Arab (contoh: بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ)

--------------------------------------------------------------------------------
CONTOH FORMAT SOAL (BISA LANGSUNG DICOPAS DARI WORD / PDF):
--------------------------------------------------------------------------------

1. Jika diketahui matriks \$A = \begin{pmatrix} 2 & 3 \\ 1 & 4 \end{pmatrix}\$, berapakah nilai determinan dari matriks A?
A. 5
B. 8
C. 11
D. -5
E. 3
KUNCI: A
BOBOT: 2

2. Sebuah mobil bergerak dengan kecepatan awal \$v_0 = 10\text{ m/s}\$ dan mengalami percepatan \$a = 2\text{ m/s}^2\$. Berapakah jarak yang ditempuh setelah \$t = 4\$ detik?
A. 40 m
B. 56 m
C. 64 m
D. 72 m
E. 80 m
KUNCI: B
BOBOT: 2

3. Kalimat berikut ini merupakan kaidah penting dalam bahasa Arab:
إِنَّمَا الأَعْمَالُ بِالنِّيَّاتِ
Apakah terjemahan yang tepat dari kalimat di atas?
A. Sesungguhnya ilmu itu cahaya
B. Setiap amal perbuatan bergantung pada niatnya
C. Bersikap jujur membawa ketenangan
D. Kebersihan adalah bagian dari iman
E. Menuntut ilmu wajib bagi setiap muslim
KUNCI: B
BOBOT: 2

4. Rumus molekul untuk senyawa glukosa adalah ...
A. \$\text{CH}_4\$
B. \$\text{H}_2\text{O}\$
C. \$\text{C}_6\text{H}_{12}\text{O}_6\$
D. \$\text{NaCl}\$
E. \$\text{CO}_2\$
KUNCI: C
BOBOT: 2

5. Tuliskan dan jelaskan 3 faktor yang mempengaruhi laju reaksi dalam ilmu kimia!
TIPE: ESSAY
BOBOT: 5

================================================================================
TEXT;

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Rekap Hasil Ujian Siswa
     */
    public function results(int $id): View
    {
        $quiz = Quiz::with([
            'classSubject.class',
            'classSubject.subject',
            'attempts.student.user',
            'attempts.answers',
        ])->findOrFail($id);

        return view('cbt.results', compact('quiz'));
    }

    /**
     * Hapus Ujian CBT
     */
    public function destroy(int $id): RedirectResponse
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return redirect()->route('cbt.index')
            ->with('success', 'Ujian CBT berhasil dihapus.');
    }
}
