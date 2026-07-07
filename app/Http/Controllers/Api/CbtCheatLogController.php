<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CbtCheatLog;
use App\Models\QuizAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CbtCheatLogController extends Controller
{
    /**
     * Record a cheating violation from the CBT client.
     * Called by the anti-cheat JS on the exam page.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'quiz_id' => 'required|integer|exists:quizzes,id',
            'violation_type' => 'required|string|in:' . implode(',', CbtCheatLog::VALID_TYPES),
            'metadata' => 'nullable|array',
        ]);

        $student = auth()->user()->student;
        if (!$student) {
            return response()->json(['message' => 'Bukan siswa.'], 403);
        }

        $attempt = QuizAttempt::where('quiz_id', $data['quiz_id'])
            ->where('student_id', $student->id)
            ->where('status', 'ongoing')
            ->first();

        if (!$attempt) {
            return response()->json(['message' => 'Tidak ada sesi ujian aktif.'], 422);
        }

        // Upsert: increment count if same violation type already logged for this attempt
        $log = CbtCheatLog::where('quiz_attempt_id', $attempt->id)
            ->where('violation_type', $data['violation_type'])
            ->first();

        if ($log) {
            $log->increment('count');
            $log->update([
                'occurred_at' => now(),
                'metadata' => $data['metadata'] ?? $log->metadata,
            ]);
        } else {
            CbtCheatLog::create([
                'quiz_attempt_id' => $attempt->id,
                'student_id' => $student->id,
                'quiz_id' => $data['quiz_id'],
                'violation_type' => $data['violation_type'],
                'count' => 1,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => $data['metadata'] ?? null,
                'occurred_at' => now(),
            ]);
        }

        $totalViolations = CbtCheatLog::where('quiz_attempt_id', $attempt->id)->sum('count');

        return response()->json([
            'logged' => true,
            'total_violations' => $totalViolations,
        ]);
    }

    /**
     * Get cheat log summary for a specific quiz attempt (for guru/admin view).
     */
    public function show(QuizAttempt $attempt): JsonResponse
    {
        $user = auth()->user();

        // Only guru who owns the quiz or kepala_sekolah can view
        if (!$user->isKepalaSekolah() && !$user->isGuru()) {
            abort(403);
        }

        $logs = $attempt->cheatLogs()
            ->orderByDesc('count')
            ->get()
            ->map(fn(CbtCheatLog $log) => [
                'type' => $log->violation_type,
                'label' => $log->violation_label,
                'count' => $log->count,
                'last_occurred' => $log->occurred_at->toIso8601String(),
                'ip_address' => $log->ip_address,
            ]);

        return response()->json([
            'attempt_id' => $attempt->id,
            'student' => $attempt->student?->name,
            'total_violations' => $logs->sum('count'),
            'violations' => $logs,
        ]);
    }
}
