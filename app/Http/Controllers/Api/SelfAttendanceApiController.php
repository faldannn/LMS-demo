<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SelfAttendanceCheckInRequest;
use App\Services\GeofenceService;
use App\Services\SelfAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SelfAttendanceApiController extends Controller
{
    public function __construct(
        private readonly SelfAttendanceService $attendanceService,
        private readonly GeofenceService $geofence,
    ) {
    }

    public function config(): JsonResponse
    {
        $student = auth()->user()->student;
        abort_unless($student, 403, 'Hanya siswa yang dapat melakukan presensi mandiri.');

        $profile = $this->attendanceService->getOrCreateFaceProfile($student);

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'photo_url' => $student->photo_url,
            ],
            'geofence' => $this->geofence->getConfig(),
            'has_face_profile' => !empty($profile->landmark_descriptor),
            'reference_descriptor' => $profile->landmark_descriptor,
        ]);
    }

    public function saveFaceProfile(Request $request): JsonResponse
    {
        $student = auth()->user()->student;
        abort_unless($student, 403);

        $data = $request->validate([
            'landmark_descriptor' => 'required|array|min:10',
            'landmark_descriptor.*' => 'numeric',
        ]);

        $profile = $this->attendanceService->saveFaceDescriptor($student, $data['landmark_descriptor']);

        return response()->json([
            'success' => true,
            'message' => 'Profil wajah berhasil disimpan.',
            'has_face_profile' => true,
            'reference_descriptor' => $profile->landmark_descriptor,
        ]);
    }

    public function checkIn(SelfAttendanceCheckInRequest $request): JsonResponse
    {
        $student = auth()->user()->student;
        abort_unless($student, 403);

        $meta = [
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $request->input('device_type'),
            'browser' => $request->input('browser'),
        ];

        $result = $this->attendanceService->processCheckIn(
            $student,
            $request->validated(),
            $request->file('photo'),
            $meta
        );

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        if ($request->filled('landmark_descriptor') && empty($student->faceProfile?->landmark_descriptor)) {
            $this->attendanceService->saveFaceDescriptor($student, $request->input('landmark_descriptor'));
        }

        return response()->json($result);
    }

    public function history(): JsonResponse
    {
        $student = auth()->user()->student;
        abort_unless($student, 403);

        $logs = $student->selfAttendanceLogs()
            ->latest('checked_in_at')
            ->limit(30)
            ->get()
            ->map(fn ($log) => [
                'date' => $log->attendance_date->format('Y-m-d'),
                'checked_in_at' => $log->checked_in_at->toIso8601String(),
                'mode' => $log->mode_label,
                'status' => $log->attendanceDetail?->status ?? 'hadir',
                'notes' => $log->notes ?: 'Scan QR Valid',
            ]);

        return response()->json(['history' => $logs]);
    }
}
