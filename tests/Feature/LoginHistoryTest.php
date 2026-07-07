<?php

namespace Tests\Feature;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_login_creates_login_log(): void
    {
        $user = User::factory()->kepalaSekolah()->create([
            'username' => 'kepsek',
            'password' => bcrypt('secret123'),
            'status' => true,
        ]);

        $this->post('/login', [
            'username' => 'kepsek',
            'password' => 'secret123',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('login_logs', [
            'user_id' => $user->id,
            'username' => 'kepsek',
            'status' => 'success',
        ]);
    }

    public function test_failed_login_creates_login_log(): void
    {
        User::factory()->guru()->create([
            'username' => 'guru1',
            'password' => bcrypt('correct'),
            'status' => true,
        ]);

        $this->post('/login', [
            'username' => 'guru1',
            'password' => 'wrong',
        ])->assertSessionHasErrors('username');

        $this->assertDatabaseHas('login_logs', [
            'username' => 'guru1',
            'status' => 'failed',
            'failure_reason' => 'invalid_credentials',
        ]);
    }

    public function test_logout_creates_activity_log(): void
    {
        $user = User::factory()->siswa()->create(['status' => true]);

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'logout',
        ]);
    }
}
