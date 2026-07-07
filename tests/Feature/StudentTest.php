<?php

namespace Tests\Feature;

use App\Models\{AcademicYear, Classes, Student, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Classes $class;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->kepalaSekolah()->create(['status' => true]);
        $year = AcademicYear::create([
            'year' => '2024/2025',
            'is_active' => true,
        ]);
        $this->class = Classes::create(['name' => 'X MIPA 1', 'grade' => 10, 'academic_year_id' => $year->id]);
    }

    public function test_admin_can_view_student_list(): void
    {
        $this->actingAs($this->admin)
            ->get(route('students.index'))
            ->assertStatus(200)
            ->assertSee('Data Siswa');
    }

    public function test_admin_can_create_student(): void
    {
        $this->actingAs($this->admin)
            ->post(route('students.store'), [
                'nis' => '2025099',
                'name' => 'Test Siswa',
                'gender' => 'L',
                'class_id' => $this->class->id,
                'status' => true,
                'create_account' => false,
            ])
            ->assertRedirect(route('students.index'));

        $this->assertDatabaseHas('students', ['nis' => '2025099', 'name' => 'Test Siswa']);
    }

    public function test_duplicate_nis_rejected(): void
    {
        Student::create(['nis' => '2025001', 'name' => 'A', 'gender' => 'L', 'class_id' => $this->class->id, 'status' => true]);

        $this->actingAs($this->admin)
            ->post(route('students.store'), [
                'nis' => '2025001',
                'name' => 'B',
                'gender' => 'L',
                'class_id' => $this->class->id,
            ])
            ->assertSessionHasErrors('nis');
    }

    public function test_admin_can_delete_student(): void
    {
        $student = Student::create([
            'nis' => '2025099',
            'name' => 'Delete Me',
            'gender' => 'L',
            'class_id' => $this->class->id,
            'status' => true
        ]);

        $this->actingAs($this->admin)
            ->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'));

        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }

    public function test_guru_cannot_access_student_management(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => true]);
        $this->actingAs($guru)
            ->get(route('students.index'))
            ->assertStatus(403);
    }
}