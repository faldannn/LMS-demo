<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_cannot_access_user_management(): void
    {
        $user = User::factory()->siswa()->create(['status' => true]);

        $this->actingAs($user)->get(route('users.index'))->assertForbidden();
    }

    public function test_guru_cannot_access_settings(): void
    {
        $user = User::factory()->guru()->create(['status' => true]);

        $this->actingAs($user)->get(route('settings.index'))->assertForbidden();
    }

    public function test_kepala_sekolah_can_access_students(): void
    {
        $user = User::factory()->kepalaSekolah()->create(['status' => true]);

        $this->actingAs($user)->get(route('students.index'))->assertOk();
    }

    public function test_chart_data_forbidden_for_siswa(): void
    {
        $user = User::factory()->siswa()->create(['status' => true]);

        $this->actingAs($user)->get(route('dashboard.chart-data'))->assertForbidden();
    }
}
