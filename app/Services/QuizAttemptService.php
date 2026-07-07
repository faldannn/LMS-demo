<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptQuestion;
use App\Models\Student;
use App\Models\StudentAnswer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuizAttemptService
{
    public function startOrResume(Quiz $quiz, Student $student): QuizAttempt
    {
        $attempt = QuizAttempt::firstOrCreate(
            ['quiz_id' => $quiz->id, 'student_id' => $student->id],
            [
                'start_time' => now(),
                'expires_at' => now()->addMinutes($quiz->duration),
                'status' => 'ongoing',
            ]
        );

        if ($attempt->wasRecentlyCreated || $attempt->attemptQuestions()->count() === 0) {
            $this->generateAttemptQuestions($quiz, $attempt);
        }

        if (!$attempt->expires_at) {
            $attempt->update(['expires_at' => $attempt->start_time->copy()->addMinutes($quiz->duration)]);
        }

        return $attempt->fresh();
    }

    public function generateAttemptQuestions(Quiz $quiz, QuizAttempt $attempt): void
    {
        $questionIds = $this->resolveQuestionIds($quiz);
        abort_if($questionIds->isEmpty(), 422, 'Ujian belum memiliki soal yang dapat dikerjakan.');

        if ($quiz->shuffle_questions) {
            $questionIds = $questionIds->shuffle()->values();
        }

        DB::transaction(function () use ($quiz, $attempt, $questionIds) {
            $attempt->attemptQuestions()->delete();

            foreach ($questionIds as $order => $questionId) {
                $optionOrder = null;
                if ($quiz->shuffle_answers) {
                    $optionOrder = Question::find($questionId)?->options()->pluck('id')->shuffle()->values()->all();
                }

                QuizAttemptQuestion::create([
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'display_order' => $order + 1,
                    'option_order' => $optionOrder,
                ]);
            }
        });
    }

    public function resolveQuestionIds(Quiz $quiz): Collection
    {
        return match ($quiz->selection_mode) {
            'random' => $this->pickRandomFromBank($quiz),
            'all_bank' => $this->allFromBank($quiz),
            default => $quiz->bankQuestions()->pluck('questions.id'),
        };
    }

    private function pickRandomFromBank(Quiz $quiz): Collection
    {
        $bankId = $quiz->question_bank_id;
        abort_unless($bankId, 422, 'Bank soal belum dikonfigurasi.');

        $count = (int) $quiz->random_question_count;
        abort_if($count < 1, 422, 'Jumlah soal acak belum diatur.');

        return Question::where('question_bank_id', $bankId)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit($count)
            ->pluck('id');
    }

    private function allFromBank(Quiz $quiz): Collection
    {
        $bankId = $quiz->question_bank_id;
        abort_unless($bankId, 422, 'Bank soal belum dikonfigurasi.');

        return Question::where('question_bank_id', $bankId)
            ->where('is_active', true)
            ->orderBy('order_number')
            ->pluck('id');
    }

    public function getAttemptQuestionsPage(QuizAttempt $attempt, int $page, int $perPage): array
    {
        $paginator = $attempt->attemptQuestions()
            ->with(['question.options'])
            ->paginate($perPage, ['*'], 'page', $page);

        $items = $paginator->getCollection()->map(function (QuizAttemptQuestion $aq) {
            $question = $aq->question;
            if ($aq->option_order) {
                $ordered = collect($aq->option_order)
                    ->map(fn ($id) => $question->options->firstWhere('id', $id))
                    ->filter();
                $question->setRelation('options', $ordered->values());
            }

            return $aq;
        });

        $paginator->setCollection($items);

        return [
            'paginator' => $paginator,
            'total' => $attempt->attemptQuestions()->count(),
        ];
    }

    public function saveAnswer(QuizAttempt $attempt, array $data): StudentAnswer
    {
        abort_if($attempt->isFinished(), 403, 'Ujian sudah selesai.');
        abort_if($attempt->isExpired(), 403, 'Waktu ujian habis.');

        $belongsToAttempt = $attempt->attemptQuestions()
            ->where('question_id', $data['question_id'])
            ->exists();
        abort_unless($belongsToAttempt, 422, 'Soal tidak valid untuk attempt ini.');

        return StudentAnswer::updateOrCreate(
            [
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $data['question_id'],
            ],
            [
                'question_option_id' => $data['option_id'] ?? null,
                'essay_answer' => $data['essay_answer'] ?? null,
            ]
        );
    }

    public function finish(QuizAttempt $attempt, string $status = 'finished'): QuizAttempt
    {
        if ($attempt->isFinished()) {
            return $attempt;
        }

        $attempt->update([
            'end_time' => now(),
            'status' => $status,
        ]);

        app(QuizService::class)->calculateScore($attempt->fresh());

        return $attempt->fresh();
    }

    public function autoSubmitIfExpired(QuizAttempt $attempt): ?QuizAttempt
    {
        if ($attempt->isFinished() || !$attempt->isExpired()) {
            return null;
        }

        return $this->finish($attempt, 'auto_submitted');
    }
}
