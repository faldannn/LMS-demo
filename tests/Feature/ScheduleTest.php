<?php

namespace Tests\Feature;

use App\Models\{AcademicYear, Classes, Schedule, Subject, Teacher, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private AcademicYear $year;
    private Classes $class;
    private Teacher $teacher;
    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->kepalaSekolah()->create(['status' => true]);
        
        $this->year = AcademicYear::create([
            'year' => '2024/2025',
            'is_active' => true,
        ]);
        
        $this->class = Classes::create([
            'name' => 'X MIPA 1',
            'grade' => 10,
            'academic_year_id' => $this->year->id,
            'session' => 'pagi',
            'room' => 'R101',
            'max_students' => 30
        ]);

        $teacherUser = User::factory()->guru()->create(['status' => true]);

        $this->teacher = Teacher::create([
            'nip' => '199001012020121001',
            'name' => 'Guru Test',
            'dob' => '1990-01-01',
            'phone' => '081234567890',
            'email' => 'guru.test@example.com',
            'status' => true,
            'user_id' => $teacherUser->id
        ]);

        $this->subject = Subject::create([
            'code' => 'MTK01',
            'name' => 'Matematika',
            'grade' => 10
        ]);
    }

    public function test_admin_can_view_schedule_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('schedules.index', ['class_id' => $this->class->id]))
            ->assertStatus(200)
            ->assertSee('Jadwal Pelajaran')
            ->assertSee('Sesi Kelas')
            ->assertSee($this->class->name);
    }

    public function test_admin_can_view_schedule_create_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('schedules.create'))
            ->assertStatus(200)
            ->assertSee('Tambah Jadwal')
            ->assertSee('Sesi Kelas')
            ->assertSee('07:00 (7 Pagi)');
    }

    public function test_admin_can_store_schedule(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('schedules.store'), [
                'class_id' => $this->class->id,
                'teacher_id' => $this->teacher->id,
                'subject' => $this->subject->name,
                'day_of_week' => 1, // Senin
                'start_time' => '08:00',
                'end_time' => '09:30',
                'room' => 'R101',
            ]);

        $response->assertRedirect(route('schedules.index', ['class_id' => $this->class->id]));
        $this->assertDatabaseHas('schedules', [
            'class_id' => $this->class->id,
            'teacher_id' => $this->teacher->id,
            'subject' => $this->subject->name,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '09:30',
        ]);
    }

    public function test_admin_can_view_schedule_edit_page(): void
    {
        $schedule = Schedule::create([
            'class_id' => $this->class->id,
            'teacher_id' => $this->teacher->id,
            'subject' => $this->subject->name,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '09:30',
            'room' => 'R101',
            'academic_year_id' => $this->year->id,
        ]);

        $this->actingAs($this->admin)
            ->get(route('schedules.edit', $schedule))
            ->assertStatus(200)
            ->assertSee('Edit Jadwal')
            ->assertSee('Form Edit Jadwal Pelajaran')
            ->assertSee('08:00 (8 Pagi)');
    }

    public function test_admin_can_update_schedule(): void
    {
        $schedule = Schedule::create([
            'class_id' => $this->class->id,
            'teacher_id' => $this->teacher->id,
            'subject' => $this->subject->name,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '09:30',
            'room' => 'R101',
            'academic_year_id' => $this->year->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('schedules.update', $schedule), [
                'class_id' => $this->class->id,
                'teacher_id' => $this->teacher->id,
                'subject' => $this->subject->name,
                'day_of_week' => 2, // Selasa
                'start_time' => '09:00',
                'end_time' => '10:30',
                'room' => 'R102',
            ]);

        $response->assertRedirect(route('schedules.index', ['class_id' => $this->class->id]));
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'day_of_week' => 2,
            'start_time' => '09:00',
            'end_time' => '10:30',
            'room' => 'R102',
        ]);
    }

    public function test_admin_can_delete_schedule(): void
    {
        $schedule = Schedule::create([
            'class_id' => $this->class->id,
            'teacher_id' => $this->teacher->id,
            'subject' => $this->subject->name,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '09:30',
            'room' => 'R101',
            'academic_year_id' => $this->year->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('schedules.destroy', $schedule));

        $response->assertRedirect(route('schedules.index', ['class_id' => $this->class->id]));
        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }
}
