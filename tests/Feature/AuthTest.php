<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_accessible(): void
    {
        $this->get('/login')->assertStatus(200)->assertSee('LMS SMA Islam Assyafiiyah 02 Bekasi');
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password123'),
            'role' => 'kepala_sekolah',
            'status' => true,
        ]);

        $this->post('/login', [
            'username' => 'testadmin',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['username' => 'guru1', 'password' => bcrypt('correct')]);

        $this->post('/login', ['username' => 'guru1', 'password' => 'wrong'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'username' => 'inactive',
            'password' => bcrypt('pass'),
            'status' => false,
        ]);

        $this->post('/login', ['username' => 'inactive', 'password' => 'pass'])
            ->assertSessionHasErrors('username');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create(['status' => true]);
        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }
}