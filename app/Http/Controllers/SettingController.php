<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(private readonly ActivityLogService $activityLogService)
    {
    }

    public function index()
    {
        // Now values are populated by AppServiceProvider through config() dynamically from DB.
        $settings = [
            'school_name'      => config('school.name', 'Nama Sekolah'),
            'school_address'   => config('school.address', ''),
            'school_phone'     => config('school.phone', ''),
            'school_email'     => config('school.email', ''),
            'school_logo'      => config('school.logo', ''),

            // Presensi Siswa Pagi
            'student_checkin_start'  => config('attendance.student_checkin_start', '06:00'),
            'late_limit'             => config('attendance.late_limit', '07:15'),
            'student_checkout_start' => config('attendance.student_checkout_start', '14:00'),
            'student_checkout_end'   => config('attendance.student_checkout_end', '17:00'),

            // Presensi Siswa Siang
            'student_checkin_start_siang'  => config('attendance.student_checkin_start_siang', '11:30'),
            'late_limit_siang'             => config('attendance.late_limit_siang', '13:00'),
            'student_checkout_start_siang' => config('attendance.student_checkout_start_siang', '17:30'),
            'student_checkout_end_siang'   => config('attendance.student_checkout_end_siang', '20:00'),

            // Presensi Guru
            'teacher_checkin_start'  => config('attendance.teacher_checkin_start', '06:00'),
            'teacher_late_limit'     => config('attendance.teacher_late_limit', '07:15'),
            'teacher_checkout_start' => config('attendance.teacher_checkout_start', '15:00'),
            'teacher_checkout_end'   => config('attendance.teacher_checkout_end', '18:00'),

            'min_attendance'   => config('attendance.min_percentage', 75),
            'geofence_lat'     => config('attendance.geofence.latitude'),
            'geofence_lng'     => config('attendance.geofence.longitude'),
            'geofence_radius'  => config('attendance.geofence.radius_meters'),
            'face_detection_min' => config('attendance.face.detection_min_confidence'),
            'face_match_min'   => config('attendance.face.match_min_confidence'),
            'academic_year'    => config('school.academic_year', date('Y') . '/' . (date('Y') + 1)),
            'school_operational_days' => Setting::get('school_operational_days', '5_days'),
            'kartu_enabled'        => (bool) Setting::get('kartu_enabled', false),
            'kartu_api_key'        => Setting::get('kartu_api_key', ''),
            'kartu_webhook_secret' => Setting::get('kartu_webhook_secret', ''),
            'kartu_last_synced_at' => Setting::get('kartu_last_synced_at', '-'),
            'kartu_last_cursor'    => Setting::get('kartu_last_cursor', '-'),
            'webhook_url'          => preg_replace('/^http:/i', 'https:', url('/api/v1/kartu/webhook')),
            'tap_attendance_api_key' => app(\App\Services\TapAttendanceService::class)->getApiKey(),
            'tap_endpoint_url'       => url('/api/v1/attendance/tap'),
            'tap_status_url'         => url('/api/v1/attendance/tap/status'),
            'feature_subject_attendance' => Setting::isFeatureEnabled('subject_attendance', true),
        ];

        return view('settings.index', compact('settings'));
    }

    public function updateFeatures(Request $request)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403, 'Akses ditolak. Pengaturan Fitur LMS hanya untuk Super Admin.');

        $data = $request->validate([
            'feature_subject_attendance' => 'nullable|boolean',
        ]);

        Setting::set('feature_subject_attendance', $request->has('feature_subject_attendance') ? '1' : '0', 'features');

        $this->activityLogService->logSettings('features', array_keys($data));

        return redirect()->route('settings.index', ['#features-pane'])
            ->with('success', 'Pengaturan Fitur Sistem LMS berhasil diperbarui.');
    }

    public function updateSchool(Request $request)
    {
        $data = $request->validate([
            'school_name'    => 'required|string|max:150',
            'school_address' => 'nullable|string|max:300',
            'school_phone'   => 'nullable|string|max:30',
            'school_email'   => 'nullable|email|max:100',
            'academic_year'  => 'nullable|string|max:20',
            'school_operational_days' => 'required|in:5_days,6_days',
        ]);

        Setting::set('school_name', $data['school_name'], 'school');
        Setting::set('school_address', $data['school_address'] ?? '', 'school');
        Setting::set('school_phone', $data['school_phone'] ?? '', 'school');
        Setting::set('school_email', $data['school_email'] ?? '', 'school');
        Setting::set('school_academic_year', $data['academic_year'] ?? '', 'school');
        Setting::set('school_operational_days', $data['school_operational_days'], 'school');

        $this->activityLogService->logSettings('school', array_keys($data));

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan sekolah berhasil disimpan.');
    }

    public function updateAttendance(Request $request)
    {
        $data = $request->validate([
            // Siswa Pagi
            'student_checkin_start'        => 'required|date_format:H:i',
            'late_limit'                   => 'required|date_format:H:i',
            'student_checkout_start'       => 'required|date_format:H:i',
            'student_checkout_end'         => 'required|date_format:H:i',

            // Siswa Siang
            'student_checkin_start_siang'  => 'required|date_format:H:i',
            'late_limit_siang'             => 'required|date_format:H:i',
            'student_checkout_start_siang' => 'required|date_format:H:i',
            'student_checkout_end_siang'   => 'required|date_format:H:i',

            // Guru
            'teacher_checkin_start'        => 'required|date_format:H:i',
            'teacher_late_limit'           => 'required|date_format:H:i',
            'teacher_checkout_start'       => 'required|date_format:H:i',
            'teacher_checkout_end'         => 'required|date_format:H:i',

            'min_attendance'               => 'required|integer|min:1|max:100',
            'geofence_lat'                 => 'required|numeric|between:-90,90',
            'geofence_lng'                 => 'required|numeric|between:-180,180',
            'geofence_radius'              => 'required|integer|min:10|max:5000',
            'face_detection_min'           => 'required|numeric|min:0.1|max:1',
            'face_match_min'               => 'required|numeric|min:0.1|max:1',
        ]);

        // Siswa Pagi
        Setting::set('att_student_checkin_start', $data['student_checkin_start'] . ':00', 'attendance');
        Setting::set('att_late_limit', $data['late_limit'] . ':00', 'attendance');
        Setting::set('att_student_checkout_start', $data['student_checkout_start'] . ':00', 'attendance');
        Setting::set('att_student_checkout_end', $data['student_checkout_end'] . ':00', 'attendance');

        // Siswa Siang
        Setting::set('att_student_checkin_start_siang', $data['student_checkin_start_siang'] . ':00', 'attendance');
        Setting::set('att_late_limit_siang', $data['late_limit_siang'] . ':00', 'attendance');
        Setting::set('att_student_checkout_start_siang', $data['student_checkout_start_siang'] . ':00', 'attendance');
        Setting::set('att_student_checkout_end_siang', $data['student_checkout_end_siang'] . ':00', 'attendance');

        // Guru
        Setting::set('att_teacher_checkin_start', $data['teacher_checkin_start'] . ':00', 'attendance');
        Setting::set('att_teacher_late_limit', $data['teacher_late_limit'] . ':00', 'attendance');
        Setting::set('att_teacher_checkout_start', $data['teacher_checkout_start'] . ':00', 'attendance');
        Setting::set('att_teacher_checkout_end', $data['teacher_checkout_end'] . ':00', 'attendance');

        Setting::set('att_min_pct', $data['min_attendance'], 'attendance');
        Setting::set('att_geofence_lat', $data['geofence_lat'], 'attendance');
        Setting::set('att_geofence_lng', $data['geofence_lng'], 'attendance');
        Setting::set('att_geofence_radius', $data['geofence_radius'], 'attendance');
        Setting::set('att_face_detection_min', $data['face_detection_min'], 'attendance');
        Setting::set('att_face_match_min', $data['face_match_min'], 'attendance');

        $this->activityLogService->logSettings('attendance', array_keys($data));

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan absensi berhasil disimpan.');
    }

    public function updateKartu(Request $request)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403, 'Akses ditolak. Fitur Integrasi RFID Kartu hanya untuk Super Admin.');

        $data = $request->validate([
            'kartu_enabled'          => 'nullable|boolean',
            'kartu_api_key'          => 'nullable|string|max:255',
            'kartu_webhook_secret'   => 'nullable|string|max:255',
            'tap_attendance_api_key' => 'nullable|string|max:255',
        ]);

        Setting::set('kartu_enabled', $request->has('kartu_enabled') ? '1' : '0', 'kartu');
        Setting::set('kartu_api_key', trim($data['kartu_api_key'] ?? ''), 'kartu');
        Setting::set('kartu_webhook_secret', trim($data['kartu_webhook_secret'] ?? ''), 'kartu');
        if (!empty($data['tap_attendance_api_key'])) {
            Setting::set('tap_attendance_api_key', trim($data['tap_attendance_api_key']), 'tap');
        }

        $this->activityLogService->logSettings('kartu', array_keys($data));

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan Integrasi Mesin Tap Kartu RFID berhasil disimpan.');
    }

    public function regenerateTapApiKey()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403, 'Akses ditolak. Fitur hanya untuk Super Admin.');

        $newKey = 'tap_nexus_' . bin2hex(random_bytes(8));
        Setting::set('tap_attendance_api_key', $newKey, 'tap');

        return redirect()->route('settings.index')
            ->with('success', 'API Key Mesin Tap baru berhasil dibuat: ' . $newKey);
    }

    public function syncKartuMembers(\App\Services\RiansaKartuService $kartuService)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403, 'Akses ditolak. Fitur Integrasi RFID Kartu hanya untuk Super Admin.');

        $result = $kartuService->syncMembers();

        if ($result['success']) {
            return redirect()->route('settings.index')
                ->with('success', $result['message']);
        }

        return redirect()->route('settings.index')
            ->with('error', $result['message']);
    }

    public function pullKartuAttendance(\App\Services\RiansaKartuService $kartuService)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403, 'Akses ditolak. Fitur Integrasi RFID Kartu hanya untuk Super Admin.');

        $result = $kartuService->pullAttendance();

        if ($result['success']) {
            return redirect()->route('settings.index')
                ->with('success', $result['message']);
        }

        return redirect()->route('settings.index')
            ->with('error', $result['message']);
    }

    public function syncCbt(\App\Services\CbtSyncService $cbtSyncService)
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin(), 403, 'Akses ditolak. Fitur Sinkronisasi CBT hanya untuk Admin dan Super Admin.');

        $result = $cbtSyncService->syncAll();

        if ($result['success']) {
            return redirect()->route('settings.index', ['#cbt-pane'])
                ->with('cbt_sync_success', true)
                ->with('success', $result['message']);
        }

        return redirect()->route('settings.index', ['#cbt-pane'])
            ->with('cbt_sync_error', true)
            ->with('error', $result['message']);
    }

}

