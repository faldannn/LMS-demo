<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeacherAccountCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_kepala_sekolah_can_create_teacher_with_default_login_account(): void
    {
        $kepalaSekolah = User::factory()->kepalaSekolah()->create(['status' => true]);

        $this->actingAs($kepalaSekolah)
            ->post(route('teachers.store'), [
                'nip' => '199001012020121001',
                'name' => 'Guru Baru',
                'dob' => '1990-01-01',
                'phone' => '08123456789',
                'email' => 'guru.baru@example.com',
                'status' => true,
            ])
            ->assertRedirect(route('teachers.index'));

        $teacher = Teacher::where('nip', '199001012020121001')->firstOrFail();
        $this->assertSame('1990-01-01', $teacher->dob->format('Y-m-d'));
        $user = $teacher->user;

        $this->assertSame('199001012020121001', $user->username);
        $this->assertSame('guru.baru@example.com', $user->email);
        $this->assertSame(UserRole::Guru, $user->role);
        $this->assertTrue(Hash::check('guru123', $user->password));

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login.post'), [
            'username' => '199001012020121001',
            'password' => 'guru123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }
}