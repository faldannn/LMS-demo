<?php

namespace Tests\Feature;

use App\Models\{AcademicYear, Classes, Student, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

        $this->assertDatabaseHas('students', ['nis' => '2025099', 'name' => 'Test Siswa', 'status' => 0]);
        $this->assertDatabaseMissing('users', ['username' => '2025099']);
    }

    public function test_admin_can_create_student_account_with_nis_login(): void
    {
        $nis = '2025100';

        $this->actingAs($this->admin)
            ->post(route('students.store'), [
                'nis' => $nis,
                'name' => 'Test Siswa Akun',
                'gender' => 'L',
                'class_id' => $this->class->id,
                'status' => true,
                'create_account' => true,
            ])
            ->assertRedirect(route('students.index'));

        $student = Student::where('nis', $nis)->firstOrFail();
        $user = User::where('username', $nis)->firstOrFail();

        $this->assertSame($user->id, $student->user_id);
        $this->assertTrue($student->status);
        $this->assertTrue($user->status);
        $this->assertTrue(Hash::check($nis, $user->password));

        $this->post(route('logout'));

        $this->post(route('login.post'), [
            'username' => $nis,
            'password' => $nis,
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_student_status_updates_toggle_login_access(): void
    {
        $nis = '2025101';

        $this->actingAs($this->admin)
            ->post(route('students.store'), [
                'nis' => $nis,
                'name' => 'Test Siswa Status',
                'gender' => 'L',
                'class_id' => $this->class->id,
                'status' => true,
                'create_account' => true,
            ])
            ->assertRedirect(route('students.index'));

        $student = Student::where('nis', $nis)->firstOrFail();
        $user = User::where('username', $nis)->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('students.update', $student), [
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'gender' => $student->gender,
                'dob' => $student->dob?->toDateString(),
                'class_id' => $student->class_id,
                'status' => false,
                'parent_name' => $student->parent_name,
                'parent_phone' => $student->parent_phone,
                'parent_email' => $student->parent_email,
                'address' => $student->address,
            ])
            ->assertRedirect(route('students.index'));

        $student->refresh();
        $user->refresh();

        $this->assertFalse($student->status);
        $this->assertFalse($user->status);

        $this->post(route('logout'));

        $this->post(route('login.post'), [
            'username' => $nis,
            'password' => $nis,
        ])->assertSessionHasErrors('username');

        $this->actingAs($this->admin)
            ->put(route('students.update', $student), [
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'gender' => $student->gender,
                'dob' => $student->dob?->toDateString(),
                'class_id' => $student->class_id,
                'status' => true,
                'parent_name' => $student->parent_name,
                'parent_phone' => $student->parent_phone,
                'parent_email' => $student->parent_email,
                'address' => $student->address,
            ])
            ->assertRedirect(route('students.index'));

        $student->refresh();
        $user->refresh();

        $this->assertTrue($student->status);
        $this->assertTrue($user->status);

        $this->post(route('logout'));

        $this->post(route('login.post'), [
            'username' => $nis,
            'password' => $nis,
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
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