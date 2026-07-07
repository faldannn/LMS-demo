<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeEssayRequest;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use App\Services\QuizService;

class QuizGradingController extends Controller
{
    public function __construct(private readonly QuizService $quizService)
    {
    }

    public function show(Quiz $quiz, QuizAttempt $attempt)
    {
        $this->authorize('update', $quiz);
        abort_unless((int) $attempt->quiz_id === (int) $quiz->id, 404);

        $attempt->load([
            'student',
            'answers.question.options',
            'answers.option',
            'attemptQuestions.question',
        ]);

        $essayAnswers = $attempt->answers->filter(
            fn (StudentAnswer $a) => $a->question?->type === 'essay'
        );

        return view('quizzes.grade', compact('quiz', 'attempt', 'essayAnswers'));
    }

    public function gradeEssay(GradeEssayRequest $request, StudentAnswer $answer)
    {
        $quiz = $answer->attempt->quiz;
        $this->authorize('update', $quiz);

        $this->quizService->gradeEssay(
            $answer,
            (float) $request->score,
            $request->feedback,
            auth()->id()
        );

        return redirect()
            ->route('quizzes.grade', [$quiz, $answer->quiz_attempt_id])
            ->with('success', 'Nilai essay berhasil disimpan.');
    }
}
