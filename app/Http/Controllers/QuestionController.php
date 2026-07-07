<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Services\QuestionBankService;
use App\Services\QuizService;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(
        private readonly QuestionBankService $bankService,
        private readonly QuizService $quizService,
    ) {
    }

    public function create(QuestionBank $questionBank)
    {
        $this->authorize('view', $questionBank);

        return view('questions.create', compact('questionBank'));
    }

    public function store(StoreQuestionRequest $request, QuestionBank $questionBank)
    {
        $this->authorize('view', $questionBank);

        $this->bankService->createQuestion(
            $questionBank,
            array_merge($request->validated(), ['created_by' => auth()->id()]),
            $request->file('image')
        );

        return redirect()->route('question-banks.show', $questionBank)
            ->with('success', 'Soal berhasil ditambahkan ke bank.');
    }

    public function destroy(Question $question)
    {
        $bank = $question->bank;
        $this->authorize('view', $bank);

        $this->bankService->deleteQuestion($question);

        return redirect()->route('question-banks.show', $bank)
            ->with('success', 'Soal berhasil dihapus.');
    }

    public function attachToQuiz(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $data = $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $this->quizService->attachQuestions($quiz, $data['question_ids']);

        return redirect()->route('quizzes.show', $quiz)
            ->with('success', count($data['question_ids']) . ' soal ditambahkan ke ujian.');
    }

    public function detachFromQuiz(Quiz $quiz, Question $question)
    {
        $this->authorize('update', $quiz);
        $this->quizService->detachQuestion($quiz, $question->id);

        return redirect()->route('quizzes.show', $quiz)
            ->with('success', 'Soal dilepas dari ujian.');
    }
}
