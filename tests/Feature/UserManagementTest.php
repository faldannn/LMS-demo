<?php

namespace Tests\Feature;

use App\Models\{AcademicYear, Classes, Student, Teacher, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
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

    public function test_deleting_student_automatically_deletes_linked_user(): void
    {
        $this->actingAs($this->admin)
            ->post(route('students.store'), [
                'nis' => '2025888',
                'name' => 'Siswa Auto Delete',
                'gender' => 'L',
                'class_id' => $this->class->id,
                'status' => true,
            ]);

        $student = Student::where('nis', '2025888')->firstOrFail();
        $userId = $student->user_id;

        $this->assertDatabaseHas('users', ['id' => $userId]);

        $this->actingAs($this->admin)
            ->from(route('students.index'))
            ->delete(route('students.destroy', $student));

        $this->assertSoftDeleted('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_deleting_user_in_user_management_automatically_deletes_linked_student(): void
    {
        $this->actingAs($this->admin)
            ->post(route('students.store'), [
                'nis' => '2025777',
                'name' => 'Siswa Delete User',
                'gender' => 'L',
                'class_id' => $this->class->id,
                'status' => true,
            ]);

        $student = Student::where('nis', '2025777')->firstOrFail();
        $user = User::where('id', $student->user_id)->firstOrFail();

        $this->actingAs($this->admin)
            ->from(route('users.index'))
            ->delete(route('users.destroy', $user));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }

    public function test_bulk_delete_users_deletes_selected_users(): void
    {
        $u1 = User::factory()->create(['role' => 'siswa', 'username' => 'u111']);
        $u2 = User::factory()->create(['role' => 'siswa', 'username' => 'u222']);
        $u3 = User::factory()->create(['role' => 'siswa', 'username' => 'u333']);

        $this->actingAs($this->admin)
            ->from(route('users.index'))
            ->delete(route('users.bulk-destroy'), [
                'user_ids' => [$u1->id, $u2->id],
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $u1->id]);
        $this->assertDatabaseMissing('users', ['id' => $u2->id]);
        $this->assertDatabaseHas('users', ['id' => $u3->id]);
    }
}
