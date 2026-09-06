<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Classes;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $guru;

    private Classes $class;

    private Teacher $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->kepalaSekolah()->create(['status' => true]);
        $this->guru = User::factory()->create(['role' => 'guru', 'status' => true]);

        $year = AcademicYear::create([
            'year' => '2024/2025',
            'is_active' => true,
        ]);
        $this->teacher = Teacher::create(['name' => 'Guru Test', 'user_id' => $this->guru->id, 'status' => true]);
        $this->class = Classes::create(['name' => 'X MIPA 1', 'grade' => 10, 'academic_year_id' => $year->id]);

        // Create 3 test students
        for ($i = 1; $i <= 3; $i++) {
            $u = User::factory()->create(['role' => 'siswa', 'status' => true]);
            Student::create([
                'nis' => "2025{$i}",
                'name' => "Siswa {$i}",
                'gender' => 'L',
                'class_id' => $this->class->id,
                'user_id' => $u->id,
                'status' => true,
            ]);
        }
    }

    public function test_attendance_create_page_accessible_by_guru(): void
    {
        Setting::set('feature_subject_attendance', '1');
        $this->actingAs($this->guru)
            ->get(route('attendance.create'))
            ->assertStatus(200);
    }

    public function test_attendance_create_redirects_when_feature_is_disabled(): void
    {
        Setting::set('feature_subject_attendance', '0');
        $this->actingAs($this->guru)->get(route('attendance.create'))->assertRedirect(route('dashboard'));
    }

    public function test_student_cannot_access_attendance_create(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa', 'status' => true]);
        $this->actingAs($siswa)
            ->get(route('attendance.create'))
            ->assertStatus(403);
    }

    public function test_admin_can_store_attendance(): void
    {
        $students = Student::all();
        $details = [];
        foreach ($students as $s) {
            $details[$s->id] = ['status' => 'hadir', 'check_in' => '07:00'];
        }

        $this->actingAs($this->admin)
            ->post(route('attendance.store'), [
                'date' => today()->toDateString(),
                'class_id' => $this->class->id,
                'session' => 'pagi',
                'details' => $details,
            ])
            ->assertRedirect(route('attendance.index'));

        $this->assertDatabaseHas('attendance', [
            'class_id' => $this->class->id,
        ]);

        $this->assertEquals(today()->toDateString(), Attendance::first()->date->toDateString());

        $attendance = Attendance::first();
        $this->assertEquals(3, $attendance->details()->count());
    }

    public function test_duplicate_attendance_is_updated_not_duplicated(): void
    {
        $students = Student::all();
        $details = [];
        foreach ($students as $s) {
            $details[$s->id] = ['status' => 'hadir'];
        }

        $payload = [
            'date' => today()->toDateString(),
            'class_id' => $this->class->id,
            'session' => 'pagi',
            'details' => $details,
        ];

        $this->actingAs($this->admin)->post(route('attendance.store'), $payload);
        $this->actingAs($this->admin)->post(route('attendance.store'), $payload);

        // Should only have 1 attendance session
        $this->assertEquals(1, Attendance::count());
    }

    public function test_attendance_percentage_calculated_correctly(): void
    {
        $student = Student::first();
        $att = Attendance::create(['date' => today(), 'class_id' => $this->class->id, 'teacher_id' => $this->teacher->id]);
        AttendanceDetail::create(['attendance_id' => $att->id, 'student_id' => $student->id, 'status' => 'hadir']);

        $att2 = Attendance::create([
            'date' => today()->subDay(),
            'class_id' => $this->class->id,
            'teacher_id' => $this->teacher->id,
        ]);
        AttendanceDetail::create(['attendance_id' => $att2->id, 'student_id' => $student->id, 'status' => 'alfa']);

        $student->refresh();
        $this->assertEquals(50.0, $student->attendance_percentage);
    }

    public function test_admin_attendance_dashboard_uses_correct_class_summary_counts(): void
    {
        $students = Student::all();
        $attendance = Attendance::create([
            'date' => today()->toDateString(),
            'class_id' => $this->class->id,
            'teacher_id' => $this->teacher->id,
            'session' => 'pagi',
        ]);

        AttendanceDetail::create([
            'attendance_id' => $attendance->id,
            'student_id' => $students[0]->id,
            'status' => 'hadir',
        ]);
        AttendanceDetail::create([
            'attendance_id' => $attendance->id,
            'student_id' => $students[1]->id,
            'status' => 'terlambat',
        ]);
        AttendanceDetail::create([
            'attendance_id' => $attendance->id,
            'student_id' => $students[2]->id,
            'status' => 'alfa',
        ]);

        // SQLite stores a model's date-cast attribute with a midnight time;
        // production MySQL DATE columns store the request value as a date.
        \Illuminate\Support\Facades\DB::table('attendance')
            ->where('id', $attendance->id)
            ->update(['date' => today()->toDateString()]);
        $this->assertSame(1, Attendance::whereBetween('date', [today()->toDateString(), today()->toDateString()])->count());

        $response = $this->actingAs($this->admin)
            ->get(route('attendance.index', ['period' => 'today']));

        $response->assertOk();
        $summary = $response->viewData('classSummaries')->first()->attendance_summary;

        $this->assertSame(1, $summary['sessions']);
        $this->assertSame(1, $summary['hadir']);
        $this->assertSame(1, $summary['terlambat']);
        $this->assertSame(1, $summary['alfa']);
        $this->assertSame(66.7, $summary['percentage']);
    }
}
