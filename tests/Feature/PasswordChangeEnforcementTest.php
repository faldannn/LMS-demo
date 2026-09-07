<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_with_default_password_must_change_it_before_accessing_dashboard(): void
    {
        $student = $this->makeStudentWithAccount('2008-04-12');

        $this->actingAs($student->user)
            ->get(route('dashboard'))
            ->assertRedirect(route('profile'));

        $this->actingAs($student->user)
            ->put(route('profile.password'), [
                'current_password' => '2008-04-12',
                'password' => 'PasswordBaru123!',
                'password_confirmation' => 'PasswordBaru123!',
            ])
            ->assertSessionHasNoErrors();

        $student->user->refresh();
        $this->assertNotNull($student->user->password_changed_at);
        $this->assertFalse($student->user->needsPasswordChange());
    }

    public function test_teacher_with_guru123_must_change_it_and_admin_reset_requires_change_again(): void
    {
        $admin = User::factory()->kepalaSekolah()->create(['status' => true]);
        $guruUser = User::factory()->guru()->create([
            'username' => '19880001',
            'password' => Hash::make('guru123'),
            'password_changed_at' => null,
            'status' => true,
        ]);
        $teacher = Teacher::create([
            'nip' => '19880001',
            'name' => 'Guru Wajib Ganti Password',
            'user_id' => $guruUser->id,
            'status' => true,
        ]);

        $this->actingAs($guruUser)
            ->get(route('dashboard'))
            ->assertRedirect(route('profile'));

        $this->actingAs($guruUser)
            ->put(route('profile.password'), [
                'current_password' => 'guru123',
                'password' => 'PasswordGuru123!',
                'password_confirmation' => 'PasswordGuru123!',
            ])
            ->assertSessionHasNoErrors();

        $guruUser->refresh();
        $this->assertNotNull($guruUser->password_changed_at);

        $this->actingAs($admin)
            ->post(route('teachers.reset-password', $teacher))
            ->assertSessionHasNoErrors();

        $guruUser->refresh();
        $this->assertTrue(Hash::check('guru123', $guruUser->password));
        $this->assertNull($guruUser->password_changed_at);
        $this->assertTrue($guruUser->needsPasswordChange());
    }

    private function makeStudentWithAccount(string $password): Student
    {
        $year = AcademicYear::create(['year' => '2026/2027', 'is_active' => true]);
        $class = Classes::create(['name' => 'X IPA 1', 'grade' => 10, 'academic_year_id' => $year->id]);
        $user = User::factory()->create([
            'username' => '0012345678',
            'password' => Hash::make($password),
            'role' => UserRole::Siswa,
            'password_changed_at' => null,
            'status' => true,
        ]);

        return Student::create([
            'nis' => '2026001',
            'nisn' => '0012345678',
            'name' => 'Siswa Wajib Ganti Password',
            'gender' => 'L',
            'dob' => $password,
            'class_id' => $class->id,
            'user_id' => $user->id,
            'status' => true,
        ]);
    }
}
