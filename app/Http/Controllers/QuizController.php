<?php

namespace App\Http\Controllers;

use App\Exports\QuizResultsExport;
use App\Http\Requests\StoreQuizRequest;
use App\Models\ClassSubject;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Services\QuizService;
use Maatwebsite\Excel\Facades\Excel;

class QuizController extends Controller
{
    public function __construct(private readonly QuizService $quizService)
    {
    }

    public function index(ClassSubject $classSubject)
    {
        $this->authorize('view', $classSubject);

        $quizzes = Quiz::where('class_subject_id', $classSubject->id)
            ->withCount('bankQuestions')
            ->with('questionBank')
            ->orderByDesc('created_at')
            ->get();

        return view('quizzes.index', compact('classSubject', 'quizzes'));
    }

    public function create(ClassSubject $classSubject)
    {
        $this->authorize('manage', $classSubject);

        $banks = QuestionBank::where('class_subject_id', $classSubject->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('quizzes.create', compact('classSubject', 'banks'));
    }

    public function store(StoreQuizRequest $request, ClassSubject $classSubject)
    {
        $this->authorize('manage', $classSubject);

        $data = $request->validated();
        $data['class_subject_id'] = $classSubject->id;

        Quiz::create($data);

        return redirect()->route('quizzes.index', $classSubject)
            ->with('success', 'Ujian CBT berhasil dibuat.');
    }

    public function show(Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $quiz->load([
            'bankQuestions.options',
            'classSubject.subject',
            'classSubject.class',
            'questionBank',
        ]);

        $availableQuestions = collect();
        if ($quiz->question_bank_id) {
            $attachedIds = $quiz->bankQuestions->pluck('id');
            $availableQuestions = Question::where('question_bank_id', $quiz->question_bank_id)
                ->where('is_active', true)
                ->whereNotIn('id', $attachedIds)
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();
        }

        return view('quizzes.show', compact('quiz', 'availableQuestions'));
    }

    public function edit(Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $banks = QuestionBank::where('class_subject_id', $quiz->class_subject_id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('quizzes.edit', compact('quiz', 'banks'));
    }

    public function update(StoreQuizRequest $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        $quiz->update($request->validated());

        return redirect()->route('quizzes.show', $quiz)
            ->with('success', 'Pengaturan ujian diperbarui.');
    }

    public function destroy(Quiz $quiz)
    {
        $this->authorize('delete', $quiz);
        $classSubjectId = $quiz->class_subject_id;
        $quiz->delete();

        return redirect()->route('quizzes.index', $classSubjectId)
            ->with('success', 'Ujian berhasil dihapus.');
    }

    public function togglePublish(Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        $quiz->update(['is_published' => !$quiz->is_published]);

        return redirect()->back()->with('success', 'Status ujian berhasil diubah.');
    }

    public function results(Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $recap = $this->quizService->getResultsRecap($quiz);
        $finished = $recap->filter(fn ($r) => $r['attempt']?->isFinished());
        $stats = [
            'total' => $recap->count(),
            'finished' => $finished->count(),
            'avg' => $finished->avg(fn ($r) => (float) $r['attempt']->score) ?? 0,
            'passed' => $finished->filter(fn ($r) => $r['passed'])->count(),
            'needs_grading' => $finished->filter(fn ($r) => $r['attempt']->needs_grading)->count(),
        ];

        return view('quizzes.results', compact('quiz', 'recap', 'stats'));
    }

    public function exportResults(Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $recap = $this->quizService->getResultsRecap($quiz);
        $filename = 'rekap-nilai-' . str($quiz->title)->slug() . '-' . now()->format('Ymd') . '.xlsx';

        return Excel::download(new QuizResultsExport($quiz, $recap), $filename);
    }
}
