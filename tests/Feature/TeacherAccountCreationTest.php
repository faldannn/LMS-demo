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
        $this->assertTrue($user->needsPasswordChange());

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login.post'), [
            'username' => '199001012020121001',
            'password' => 'guru123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertRedirect(route('profile'));
    }

    public function test_kepala_sekolah_can_update_teacher_and_change_password(): void
    {
        $kepalaSekolah = User::factory()->kepalaSekolah()->create(['status' => true]);
        
        $user = User::factory()->guru()->create([
            'username' => '199001012020121001',
            'password' => Hash::make('password_lama'),
            'name' => 'Guru Lama',
            'email' => 'guru.lama@example.com',
            'status' => true,
        ]);
        
        $teacher = Teacher::create([
            'nip' => '199001012020121001',
            'name' => 'Guru Lama',
            'dob' => '1990-01-01',
            'email' => 'guru.lama@example.com',
            'user_id' => $user->id,
            'status' => true,
        ]);

        $this->actingAs($kepalaSekolah)
            ->put(route('teachers.update', $teacher), [
                'nip' => '199001012020121001',
                'name' => 'Guru Edit',
                'dob' => '1990-01-02',
                'phone' => '08123456789',
                'email' => 'guru.edit@example.com',
                'status' => true,
                'password' => 'password_baru',
            ])
            ->assertRedirect(route('teachers.index'));

        $teacher->refresh();
        $this->assertSame('Guru Edit', $teacher->name);
        $this->assertSame('1990-01-02', $teacher->dob->format('Y-m-d'));
        
        $user->refresh();
        $this->assertSame('guru.edit@example.com', $user->email);
        $this->assertTrue(Hash::check('password_baru', $user->password));
    }
}
