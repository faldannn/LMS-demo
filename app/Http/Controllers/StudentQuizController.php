<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveQuizAnswerRequest;
use App\Models\ClassSubject;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizAttemptService;
use App\Services\QuizService;
use Illuminate\Http\Request;

class StudentQuizController extends Controller
{
    public function __construct(
        private readonly QuizService $quizService,
        private readonly QuizAttemptService $attemptService,
    ) {
    }

    public function index()
    {
        $student = auth()->user()->student;

        if (!$student || !$student->class_id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar dalam kelas apapun.');
        }

        $classSubjects = ClassSubject::with(['subject', 'teacher', 'quizzes' => function ($query) {
            $query->where('is_published', true)->orderBy('start_time', 'asc');
        }, 'quizzes.attempts' => function ($query) use ($student) {
            $query->where('student_id', $student->id);
        }])
            ->where('class_id', $student->class_id)
            ->get();

        return view('student-quizzes.index', compact('classSubjects'));
    }

    public function show(Quiz $quiz)
    {
        $this->authorize('view', $quiz);

        $student = auth()->user()->student;
        $attempt = $quiz->attempts()->where('student_id', $student->id)->first();
        $quiz->loadCount(['bankQuestions as questions_count']);

        return view('student-quizzes.show', compact('quiz', 'attempt'));
    }

    public function start(Quiz $quiz)
    {
        $this->authorize('attempt', $quiz);

        if (!$quiz->isOpen()) {
            return redirect()->back()->with('error', 'Kuis ini belum dimulai atau sudah ditutup.');
        }

        $student = auth()->user()->student;

        try {
            $attempt = $this->attemptService->startOrResume($quiz, $student);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return redirect()->back()->with('error', $e->getMessage() ?: 'Ujian belum siap dikerjakan.');
        }

        if ($attempt->isFinished()) {
            return redirect()->route('student.quizzes.result', $quiz);
        }

        return redirect()->route('student.quizzes.attempt', $quiz);
    }

    public function attempt(Request $request, Quiz $quiz)
    {
        $this->authorize('attempt', $quiz);

        $student = auth()->user()->student;
        $attempt = $quiz->attempts()->where('student_id', $student->id)->first();

        if (!$attempt) {
            return redirect()->route('student.quizzes.show', $quiz);
        }

        if ($attempt->isFinished()) {
            return redirect()->route('student.quizzes.result', $quiz);
        }

        if ($auto = $this->attemptService->autoSubmitIfExpired($attempt)) {
            return redirect()->route('student.quizzes.result', $quiz)
                ->with('info', 'Waktu habis — jawaban otomatis dikumpulkan.');
        }

        $page = max(1, (int) $request->get('page', 1));
        $perPage = max(1, (int) $quiz->questions_per_page);
        $result = $this->attemptService->getAttemptQuestionsPage($attempt, $page, $perPage);
        $savedAnswers = $attempt->answers()->get()->keyBy('question_id');

        return view('student-quizzes.attempt', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'paginator' => $result['paginator'],
            'totalQuestions' => $result['total'],
            'savedAnswers' => $savedAnswers,
            'currentPage' => $page,
        ]);
    }

    public function timeRemaining(Quiz $quiz)
    {
        $student = auth()->user()->student;
        $attempt = $quiz->attempts()->where('student_id', $student->id)->where('status', 'ongoing')->first();

        if (!$attempt) {
            return response()->json(['remaining' => 0, 'expired' => true]);
        }

        if ($attempt->isExpired()) {
            $this->attemptService->autoSubmitIfExpired($attempt);

            return response()->json(['remaining' => 0, 'expired' => true]);
        }

        return response()->json([
            'remaining' => $attempt->remainingSeconds(),
            'expired' => false,
        ]);
    }

    public function saveAnswer(SaveQuizAnswerRequest $request, Quiz $quiz)
    {
        $student = auth()->user()->student;
        $attempt = $quiz->attempts()->where('student_id', $student->id)->where('status', 'ongoing')->first();

        if (!$attempt) {
            return response()->json(['success' => false, 'message' => 'Ujian sudah selesai atau belum dimulai.']);
        }

        if ($this->attemptService->autoSubmitIfExpired($attempt)) {
            return response()->json(['success' => false, 'expired' => true]);
        }

        $this->attemptService->saveAnswer($attempt, $request->validated());

        return response()->json(['success' => true]);
    }

    public function finish(Quiz $quiz)
    {
        $student = auth()->user()->student;
        $attempt = $quiz->attempts()->where('student_id', $student->id)->where('status', 'ongoing')->first();

        if ($attempt) {
            $this->attemptService->finish($attempt, 'finished');
        }

        return redirect()->route('student.quizzes.result', $quiz)
            ->with('success', 'Ujian selesai. Terima kasih.');
    }

    public function autoSubmit(Quiz $quiz)
    {
        $student = auth()->user()->student;
        $attempt = $quiz->attempts()->where('student_id', $student->id)->where('status', 'ongoing')->first();

        if ($attempt) {
            $this->attemptService->finish($attempt, 'auto_submitted');
        }

        return response()->json(['success' => true, 'redirect' => route('student.quizzes.result', $quiz)]);
    }

    public function result(Quiz $quiz)
    {
        $student = auth()->user()->student;
        $attempt = $quiz->attempts()->where('student_id', $student->id)->first();

        if (!$attempt || !$attempt->isFinished()) {
            return redirect()->route('student.quizzes.index');
        }

        if (!$quiz->show_results) {
            return view('student-quizzes.result-hidden', compact('quiz', 'attempt'));
        }

        $answers = $attempt->answers()->with('question', 'option')->get()->keyBy('question_id');

        return view('student-quizzes.result', compact('quiz', 'attempt', 'answers'));
    }
}
