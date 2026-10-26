<?php

namespace App\Http\Controllers;

use App\Services\TeacherAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAttendanceController extends Controller
{
    public function __construct(private readonly TeacherAttendanceService $attendanceService)
    {
    }

    public function checkinView()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('dashboard')
                ->with('error', 'Data guru Anda tidak ditemukan.');
        }

        $todayAttendance = $this->attendanceService->getTodayAttendance($teacher);
        $checkinStart = config('attendance.teacher_checkin_start', '06:00:00');
        $lateLimit = config('attendance.teacher_late_limit', '07:15:00');
        $checkoutStart = config('attendance.teacher_checkout_start', '15:00:00');
        $checkoutEnd = config('attendance.teacher_checkout_end', '18:00:00');

        return view('attendance.teacher-checkin', compact('teacher', 'todayAttendance', 'checkinStart', 'lateLimit', 'checkoutStart', 'checkoutEnd'));
    }

    public function status(): JsonResponse
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Guru tidak ditemukan.'], 404);
        }

        $todayAttendance = $this->attendanceService->getTodayAttendance($teacher);

        return response()->json([
            'success' => true,
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'nip' => $teacher->nip,
            ],
            'attendance' => $todayAttendance ? [
                'status' => $todayAttendance->status,
                'check_in_at' => $todayAttendance->check_in_at?->format('H:i:s'),
                'check_in_photo' => $todayAttendance->check_in_photo ? asset('storage/' . $todayAttendance->check_in_photo) : null,
                'check_out_at' => $todayAttendance->check_out_at?->format('H:i:s'),
                'check_out_photo' => $todayAttendance->check_out_photo ? asset('storage/' . $todayAttendance->check_out_photo) : null,
            ] : null,
            'config' => [
                'teacher_checkin_start' => config('attendance.teacher_checkin_start', '06:00:00'),
                'teacher_late_limit' => config('attendance.teacher_late_limit', '07:15:00'),
                'teacher_checkout_start' => config('attendance.teacher_checkout_start', '15:00:00'),
                'teacher_checkout_end' => config('attendance.teacher_checkout_end', '18:00:00'),
                'geofence_lat' => (float) config('attendance.geofence.latitude'),
                'geofence_lng' => (float) config('attendance.geofence.longitude'),
                'geofence_radius' => (int) config('attendance.geofence.radius_meters'),
            ],
        ]);
    }

    public function checkIn(Request $request): JsonResponse
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Guru tidak ditemukan.'], 404);
        }

        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:10240',
            'photo_base64' => 'nullable|string',
            'check_in_mode' => 'nullable|string',
        ]);

        $result = $this->attendanceService->processCheckIn(
            $teacher,
            $request->only(['latitude', 'longitude', 'check_in_mode', 'photo_base64']),
            $request->file('photo'),
            [
                'user_id' => $user->id,
                'device_type' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
            ]
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function checkOut(Request $request): JsonResponse
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Guru tidak ditemukan.'], 404);
        }

        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:10240',
            'photo_base64' => 'nullable|string',
            'check_out_mode' => 'nullable|string',
        ]);

        $result = $this->attendanceService->processCheckOut(
            $teacher,
            $request->only(['latitude', 'longitude', 'check_out_mode', 'photo_base64']),
            $request->file('photo'),
            [
                'user_id' => $user->id,
                'device_type' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
            ]
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
