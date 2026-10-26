<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;
use App\Models\StudentAnswer;
use App\Services\CbtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CbtStudentController extends Controller
{
    public function __construct(
        protected CbtService $cbtService
    ) {}

    /**
     * Dashboard Ujian CBT untuk Siswa
     */
    public function index(): View
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Akun Anda tidak terhubung dengan profil siswa.');
        }

        // Ambil semua ujian yang ditujukan untuk kelas siswa
        $quizzes = Quiz::whereHas('classSubject', function ($q) use ($student) {
            $q->where('class_id', $student->class_id);
        })
        ->where('is_published', true)
        ->with(['classSubject.subject', 'classSubject.teacher.user'])
        ->withCount('questions')
        ->latest()
        ->get();

        // Ambil attempt siswa untuk setiap ujian
        $attempts = QuizAttempt::where('student_id', $student->id)
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()
            ->keyBy('quiz_id');

        return view('cbt.student-index', compact('quizzes', 'attempts', 'student'));
    }

    /**
     * Verifikasi Token 5-Menit dan Mulai Pengerjaan Ujian
     */
    public function start(Request $request, int $quizId): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'token' => 'required|string|min:4|max:10',
        ]);

        $quiz = Quiz::with('classSubject')->findOrFail($quizId);

        // Pastikan kelas siswa cocok
        if ($quiz->classSubject->class_id !== $student->class_id) {
            return redirect()->back()->with('error', 'Ujian ini bukan untuk kelas Anda.');
        }

        try {
            $attempt = $this->cbtService->startAttempt($quiz, $student, $request->token);

            return redirect()->route('cbt.student.exam', $attempt->id);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Ruang Ujian Siswa (Fullscreen Modern CBT Layout)
     */
    public function examRoom(int $attemptId): View|RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        $attempt = QuizAttempt::with(['quiz.classSubject.subject', 'answers'])
            ->findOrFail($attemptId);

        if ($attempt->student_id !== $student->id) {
            abort(403, 'Anda tidak memiliki akses ke sesi ujian ini.');
        }

        // Jika waktu ujian sudah habis otomatis submit
        if ($attempt->isOngoing() && $attempt->expires_at && now()->gt($attempt->expires_at)) {
            $this->cbtService->finishAttempt($attempt);
            return redirect()->route('cbt.student.result', $attempt->id)
                ->with('warning', 'Waktu pengerjaan ujian telah berakhir. Jawaban Anda berhasil dikirim secara otomatis.');
        }

        if ($attempt->isCompleted()) {
            return redirect()->route('cbt.student.result', $attempt->id);
        }

        $quiz = $attempt->quiz;
        $questions = Question::with('options')
            ->where('quiz_id', $quiz->id)
            ->where('is_active', true)
            ->orderBy('order_number')
            ->get();

        // Map jawaban yang sudah tersimpan
        $savedAnswers = $attempt->answers->keyBy('question_id');

        return view('cbt.exam-room', compact('attempt', 'quiz', 'questions', 'savedAnswers', 'student'));
    }

    /**
     * Autosave Jawaban Siswa secara Real-time via AJAX
     */
    public function saveAnswer(Request $request, int $attemptId): JsonResponse
    {
        $user = Auth::user();
        $student = $user->student;

        $attempt = QuizAttempt::findOrFail($attemptId);

        if ($attempt->student_id !== $student->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$attempt->isOngoing()) {
            return response()->json(['success' => false, 'message' => 'Sesi ujian telah berakhir'], 400);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'question_option_id' => 'nullable|exists:question_options,id',
            'essay_answer' => 'nullable|string',
        ]);

        try {
            $answer = $this->cbtService->saveAnswer(
                $attempt,
                (int) $request->question_id,
                $request->question_option_id ? (int) $request->question_option_id : null,
                $request->essay_answer
            );

            $answeredCount = StudentAnswer::where('quiz_attempt_id', $attempt->id)
                ->where(function ($q) {
                    $q->whereNotNull('question_option_id')
                      ->orWhereNotNull('essay_answer');
                })
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Jawaban tersimpan',
                'answered_count' => $answeredCount,
                'answer_id' => $answer->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Selesaikan Ujian Siswa (Submit)
     */
    public function finish(Request $request, int $attemptId): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        $attempt = QuizAttempt::findOrFail($attemptId);

        if ($attempt->student_id !== $student->id) {
            abort(403, 'Akses ditolak.');
        }

        $attempt = $this->cbtService->finishAttempt($attempt);

        return redirect()->route('cbt.student.result', $attempt->id)
            ->with('success', 'Ujian berhasil diselesaikan! Terima kasih telah mengikuti ujian dengan jujur.');
    }

    /**
     * Halaman Ringkasan Hasil Ujian Siswa
     */
    public function result(int $attemptId): View
    {
        $user = Auth::user();
        $student = $user->student;

        $attempt = QuizAttempt::with([
            'quiz.classSubject.subject',
            'answers.question.options',
        ])->findOrFail($attemptId);

        if ($attempt->student_id !== $student->id) {
            abort(403, 'Akses ditolak.');
        }

        return view('cbt.student-result', compact('attempt', 'student'));
    }
}
