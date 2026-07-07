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
            'late_limit'       => config('attendance.late_limit', '07:15'),
            'min_attendance'   => config('attendance.min_percentage', 75),
            'geofence_lat'     => config('attendance.geofence.latitude'),
            'geofence_lng'     => config('attendance.geofence.longitude'),
            'geofence_radius'  => config('attendance.geofence.radius_meters'),
            'face_detection_min' => config('attendance.face.detection_min_confidence'),
            'face_match_min'   => config('attendance.face.match_min_confidence'),
            'academic_year'    => config('school.academic_year', date('Y') . '/' . (date('Y') + 1)),
        ];

        return view('settings.index', compact('settings'));
    }

    public function updateSchool(Request $request)
    {
        $data = $request->validate([
            'school_name'    => 'required|string|max:150',
            'school_address' => 'nullable|string|max:300',
            'school_phone'   => 'nullable|string|max:30',
            'school_email'   => 'nullable|email|max:100',
            'academic_year'  => 'nullable|string|max:20',
        ]);

        Setting::set('school_name', $data['school_name'], 'school');
        Setting::set('school_address', $data['school_address'] ?? '', 'school');
        Setting::set('school_phone', $data['school_phone'] ?? '', 'school');
        Setting::set('school_email', $data['school_email'] ?? '', 'school');
        Setting::set('school_academic_year', $data['academic_year'] ?? '', 'school');

        $this->activityLogService->logSettings('school', array_keys($data));

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan sekolah berhasil disimpan.');
    }

    public function updateAttendance(Request $request)
    {
        $data = $request->validate([
            'late_limit'     => 'required|date_format:H:i',
            'min_attendance' => 'required|integer|min:1|max:100',
            'geofence_lat'   => 'required|numeric|between:-90,90',
            'geofence_lng'   => 'required|numeric|between:-180,180',
            'geofence_radius'=> 'required|integer|min:10|max:5000',
            'face_detection_min' => 'required|numeric|min:0.1|max:1',
            'face_match_min' => 'required|numeric|min:0.1|max:1',
        ]);

        Setting::set('att_late_limit', $data['late_limit'] . ':00', 'attendance');
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
}
