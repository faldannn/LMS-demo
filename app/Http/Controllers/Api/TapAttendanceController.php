<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\TapAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TapAttendanceController extends Controller
{
    public function __construct(
        private readonly TapAttendanceService $tapService
    ) {}

    /**
     * Endpoint: POST /api/v1/attendance/tap
     * Menerima request tap dari Mesin Tap Absensi / IoT Reader.
     */
    public function handle(Request $request): JsonResponse
    {
        // 1. Verifikasi Keamanan / API Key
        $providedKey = $request->header('X-Api-Key')
            ?? $request->header('X-Tap-Secret')
            ?? $request->bearerToken()
            ?? $request->input('api_key')
            ?? $request->input('secret');

        // Bolehkan jika request memiliki valid API Key ATAU berasal dari web session admin/superadmin yang login (untuk simulator)
        $isWebAdmin = auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin());

        if (!$isWebAdmin && !$this->tapService->validateApiKey($providedKey)) {
            Log::warning('Tap Attendance API: Unauthorized attempt.', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'status' => 'error',
                'action' => 'unauthorized',
                'message' => 'Akses ditolak: API Key tidak valid atau belum dikonfigurasi.',
            ], 401);
        }

        // 2. Ambil identifier kartu (mendukung berbagai format payload dari mesin)
        $identifier = $request->input('card_id')
            ?? $request->input('rfid')
            ?? $request->input('ident')
            ?? $request->input('nisn')
            ?? $request->input('nis')
            ?? $request->input('uid')
            ?? $request->input('code');

        if (empty($identifier)) {
            return response()->json([
                'status' => 'error',
                'action' => 'bad_request',
                'message' => 'Parameter kartu (card_id / rfid / nisn / nis) wajib diisi.',
            ], 400);
        }

        $deviceId = (string) ($request->input('device_id') ?? $request->header('X-Device-Id') ?? 'TAP-GATE-01');
        $rawTime = $request->input('timestamp');
        $customNotes = $request->input('notes');

        // 3. Proses absensi tap
        $result = $this->tapService->recordTap(
            identifier: (string) $identifier,
            deviceId: $deviceId,
            rawTime: $rawTime,
            customNotes: $customNotes
        );

        $httpCode = $result['code'] ?? ($result['success'] ? 200 : 400);
        unset($result['code']);

        return response()->json($result, $httpCode);
    }

    /**
     * Endpoint: GET /api/v1/attendance/tap/status
     * Health check untuk mesin tap / IoT microcontroller (ESP32 / Arduino / Android / Raspberry Pi).
     */
    public function status(Request $request): JsonResponse
    {
        $schoolName = Setting::get('school_name', config('app.name', 'LMS Assyafiiyah'));
        $lateLimit = Setting::get('late_threshold', config('attendance.late_limit', '07:15'));

        return response()->json([
            'status' => 'online',
            'system' => 'LMS Tap Attendance Integration Layer',
            'server_time' => now()->toDateTimeString(),
            'school_name' => $schoolName,
            'late_threshold' => $lateLimit,
            'supported_identifiers' => ['rfid_uid', 'nisn', 'nis'],
            'endpoint' => url('/api/v1/attendance/tap'),
        ]);
    }
}
