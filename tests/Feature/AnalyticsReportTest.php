<?php

namespace Tests\Feature;

use App\Models\{
    AcademicYear,
    Attendance,
    AttendanceDetail,
    Classes,
    Semester,
    Student,
    Subject,
    Teacher,
    User,
};
use App\Services\Reports\ComprehensiveReportService;
use App\Support\ReportFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AnalyticsReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Classes $class;
    private Teacher $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->kepalaSekolah()->create(['status' => true]);
        $guruUser = User::factory()->create(['role' => 'guru', 'status' => true]);

        $year = AcademicYear::create(['year' => '2024/2025', 'is_active' => true]);
        Semester::create([
            'academic_year_id' => $year->id,
            'number' => 1,
            'start_date' => '2024-07-01',
            'end_date' => '2024-12-31',
            'is_active' => true,
        ]);

        $this->teacher = Teacher::create(['name' => 'Guru Laporan', 'nip' => '198001', 'user_id' => $guruUser->id, 'status' => true]);
        $this->class = Classes::create(['name' => 'X IPA 1', 'grade' => 10, 'academic_year_id' => $year->id]);

        $studentUser = User::factory()->create(['role' => 'siswa', 'status' => true]);
        $student = Student::create([
            'nis' => '2025001',
            'name' => 'Siswa Laporan',
            'gender' => 'L',
            'class_id' => $this->class->id,
            'user_id' => $studentUser->id,
            'status' => true,
        ]);

        $attendance = Attendance::create([
            'date' => today(),
            'class_id' => $this->class->id,
            'teacher_id' => $this->teacher->id,
            'session' => 'pagi',
        ]);

        AttendanceDetail::create([
            'attendance_id' => $attendance->id,
            'student_id' => $student->id,
            'status' => 'hadir',
        ]);
    }

    public function test_reports_hub_accessible_by_kepala_sekolah(): void
    {
        $this->actingAs($this->admin)
            ->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Laporan Analitik')
            ->assertSee('Kehadiran Guru');
    }

    public function test_all_report_types_render(): void
    {
        foreach (array_keys(config('reports.types')) as $type) {
            $this->actingAs($this->admin)
                ->get(route('reports.show', $type))
                ->assertOk();
        }
    }

    public function test_report_pdf_and_excel_exports(): void
    {
        foreach (array_keys(config('reports.types')) as $type) {
            $this->actingAs($this->admin)
                ->get(route('reports.type.pdf', $type))
                ->assertOk();

            $this->actingAs($this->admin)
                ->get(route('reports.type.excel', $type))
                ->assertOk()
                ->assertDownload();
        }
    }

    public function test_teacher_attendance_service_returns_data(): void
    {
        $filter = ReportFilter::fromRequest(Request::create('/', 'GET', [
            'date_from' => today()->toDateString(),
            'date_to' => today()->toDateString(),
        ]));

        $data = app(ComprehensiveReportService::class)->teacherAttendance($filter);

        $this->assertGreaterThan(0, $data['rows']->count());
        $this->assertSame('Guru Laporan', $data['rows']->first()['name']);
    }

    public function test_student_attendance_service_returns_data(): void
    {
        $filter = ReportFilter::fromRequest(Request::create('/', 'GET', [
            'date_from' => today()->toDateString(),
            'date_to' => today()->toDateString(),
            'class_id' => $this->class->id,
        ]));

        $data = app(ComprehensiveReportService::class)->studentAttendance($filter);

        $this->assertGreaterThan(0, $data['rows']->count());
        $this->assertSame('2025001', $data['rows']->first()['nis']);
    }

    public function test_invalid_report_type_returns_404(): void
    {
        $this->actingAs($this->admin)
            ->get('/reports/invalid-type')
            ->assertNotFound();
    }
}
