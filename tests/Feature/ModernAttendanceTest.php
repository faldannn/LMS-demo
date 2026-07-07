<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModernAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $siswaUser;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        config([
            'attendance.geofence.latitude' => -6.2349,
            'attendance.geofence.longitude' => 106.9896,
            'attendance.geofence.radius_meters' => 500,
            'attendance.face.detection_min_confidence' => 0.5,
            'attendance.face.match_min_confidence' => 0.5,
        ]);

        $year = AcademicYear::create(['year' => '2025/2026', 'is_active' => true]);
        $guru = User::factory()->guru()->create(['status' => true]);
        $teacher = Teacher::create(['name' => 'Wali Kelas', 'user_id' => $guru->id, 'status' => true]);
        $class = Classes::create([
            'name' => 'X IPA',
            'grade' => 10,
            'academic_year_id' => $year->id,
            'homeroom_teacher_id' => $teacher->id,
        ]);
        $this->siswaUser = User::factory()->siswa()->create(['status' => true]);
        $this->student = Student::create([
            'nis' => '2025201',
            'name' => 'Siswa Presensi',
            'gender' => 'L',
            'class_id' => $class->id,
            'user_id' => $this->siswaUser->id,
            'status' => true,
        ]);
    }

    public function test_student_can_load_self_attendance_config_api(): void
    {
        $this->actingAs($this->siswaUser)
            ->getJson('/api/attendance/self/config')
            ->assertOk()
            ->assertJsonPath('student.nis', '2025201')
            ->assertJsonStructure(['geofence' => ['latitude', 'longitude', 'radius_meters']]);
    }

    public function test_self_checkin_with_gps_fallback_succeeds(): void
    {
        $photo = UploadedFile::fake()->image('face.jpg');

        $this->actingAs($this->siswaUser)
            ->post('/api/attendance/self/check-in', [
                'check_in_mode' => 'gps_fallback',
                'latitude' => -6.2349,
                'longitude' => 106.9896,
                'face_detection_confidence' => 0,
                'face_match_confidence' => 0,
                'face_matched' => false,
                'device_type' => 'mobile',
                'browser' => 'Chrome',
                'photo' => $photo,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('self_attendance_logs', [
            'student_id' => $this->student->id,
            'check_in_mode' => 'gps_fallback',
            'within_radius' => true,
        ]);
    }

    public function test_checkin_rejected_when_outside_radius(): void
    {
        $this->actingAs($this->siswaUser)
            ->postJson('/api/attendance/self/check-in', [
                'check_in_mode' => 'full',
                'latitude' => -6.5,
                'longitude' => 107.5,
                'face_detection_confidence' => 0.9,
                'face_match_confidence' => 0.9,
                'face_matched' => true,
            ])
            ->assertStatus(422);
    }

    public function test_self_checkin_page_accessible(): void
    {
        $this->actingAs($this->siswaUser)
            ->get(route('attendance.self-checkin'))
            ->assertOk()
            ->assertSee('Presensi Mandiri');
    }

    public function test_student_dashboard_prioritizes_face_and_gps_attendance(): void
    {
        $this->actingAs($this->siswaUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Presensi Wajah & GPS', false)
            ->assertDontSee('Scan QR Absensi');
    }
}
