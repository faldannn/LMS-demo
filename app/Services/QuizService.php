<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function __construct(
        private readonly FileStorageService $fileStorage,
    ) {
    }

    public function attachQuestions(Quiz $quiz, array $questionIds): void
    {
        $order = (int) $quiz->bankQuestions()->max('quiz_questions.order_number');
        $sync = [];
        foreach ($questionIds as $questionId) {
            $sync[$questionId] = ['order_number' => ++$order];
        }
        $quiz->bankQuestions()->syncWithoutDetaching($sync);
    }

    public function detachQuestion(Quiz $quiz, int $questionId): void
    {
        $quiz->bankQuestions()->detach($questionId);
    }

    public function calculateScore(QuizAttempt $attempt): float
    {
        $attempt->load(['quiz', 'answers']);

        $questionIds = $attempt->attemptQuestions()->pluck('question_id');
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');
        $pivotPoints = $attempt->quiz->bankQuestions()
            ->whereIn('questions.id', $questionIds)
            ->get()
            ->keyBy('id');

        $correctOptions = QuestionOption::whereIn('question_id', $questionIds)
            ->where('is_correct', true)
            ->pluck('id', 'question_id');

        $totalPoints = 0;
        $earnedPoints = 0;
        $needsGrading = false;

        foreach ($questionIds as $qid) {
            $question = $questions->get($qid);
            if (!$question) {
                continue;
            }

            $points = (int) ($pivotPoints->get($qid)?->pivot->points_override ?? $question->points);
            $totalPoints += $points;

            $answer = $attempt->answers->firstWhere('question_id', $qid);

            if ($question->type === 'multiple_choice') {
                $isCorrect = $answer
                    && $correctOptions->get($qid)
                    && (int) $answer->question_option_id === (int) $correctOptions->get($qid);

                $score = $isCorrect ? $points : 0;
                $earnedPoints += $score;

                if ($answer) {
                    $answer->update([
                        'is_correct' => $isCorrect,
                        'score' => $score,
                    ]);
                }
            } elseif ($answer && $answer->score !== null) {
                $earnedPoints += (float) $answer->score;
            } elseif ($answer && $answer->essay_answer) {
                $needsGrading = true;
            }
        }

        $finalScore = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;

        $attempt->update([
            'score' => $finalScore,
            'total_points' => $totalPoints,
            'earned_points' => $earnedPoints,
            'needs_grading' => $needsGrading,
        ]);

        return $finalScore;
    }

    public function gradeEssay(StudentAnswer $answer, float $score, ?string $feedback, int $graderId): void
    {
        DB::transaction(function () use ($answer, $score, $feedback, $graderId) {
            $maxPoints = $answer->question->effectivePoints($answer->attempt->quiz);
            $score = min(max(0, $score), $maxPoints);

            $answer->update([
                'score' => $score,
                'feedback' => $feedback,
                'graded_by' => $graderId,
                'graded_at' => now(),
            ]);

            $this->calculateScore($answer->attempt->fresh(['answers']));
        });
    }

    public function getResultsRecap(Quiz $quiz): Collection
    {
        $students = $quiz->classSubject->class->students()->where('status', true)->orderBy('name')->get();
        $attempts = $quiz->attempts()->with('student')->get()->keyBy('student_id');

        return $students->map(function ($student) use ($attempts, $quiz) {
            $attempt = $attempts->get($student->id);

            return [
                'student' => $student,
                'attempt' => $attempt,
                'passed' => $attempt && $attempt->isFinished() && (float) $attempt->score >= (float) $quiz->pass_score,
            ];
        });
    }

    public function uploadImage(UploadedFile $file): string
    {
        return $this->fileStorage->upload($file, 'questions')['file_path'];
    }
}
