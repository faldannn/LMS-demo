<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionBankRequest;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\ClassSubject;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Services\QuestionBankService;

class QuestionBankController extends Controller
{
    public function __construct(private readonly QuestionBankService $bankService)
    {
    }

    public function index(ClassSubject $classSubject)
    {
        $this->authorize('view', $classSubject);

        $banks = $this->bankService->listForClassSubject(
            $classSubject->id,
            request()->only('search')
        );

        return view('question-banks.index', compact('classSubject', 'banks'));
    }

    public function create(ClassSubject $classSubject)
    {
        $this->authorize('manage', $classSubject);

        return view('question-banks.create', compact('classSubject'));
    }

    public function store(StoreQuestionBankRequest $request, ClassSubject $classSubject)
    {
        $this->authorize('manage', $classSubject);

        $bank = $this->bankService->createBank([
            'class_subject_id' => $classSubject->id,
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('question-banks.show', $bank)
            ->with('success', 'Bank soal berhasil dibuat.');
    }

    public function show(QuestionBank $questionBank)
    {
        $this->authorize('view', $questionBank);

        $questionBank->load('classSubject.subject', 'classSubject.class');
        $questions = $this->bankService->listQuestions($questionBank, request()->only('type', 'difficulty', 'search'));

        return view('question-banks.show', compact('questionBank', 'questions'));
    }
}
