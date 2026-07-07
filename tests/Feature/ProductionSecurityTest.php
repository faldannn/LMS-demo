<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProductionSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->kepalaSekolah()->create(['status' => true]);
    }

    public function test_security_hub_accessible_by_kepala_sekolah(): void
    {
        $this->actingAs($this->admin)
            ->get(route('security.index'))
            ->assertOk()
            ->assertSee('Keamanan Sistem');
    }

    public function test_guru_cannot_access_security_hub(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => true]);

        $this->actingAs($guru)
            ->get(route('security.index'))
            ->assertForbidden();
    }

    public function test_audit_log_page_renders(): void
    {
        $this->actingAs($this->admin)
            ->get(route('security.audit-logs'))
            ->assertOk();
    }

    public function test_login_history_page_renders(): void
    {
        $this->actingAs($this->admin)
            ->get(route('security.login-history'))
            ->assertOk();
    }

    public function test_session_management_page_renders(): void
    {
        $this->actingAs($this->admin)
            ->get(route('security.sessions'))
            ->assertOk();
    }

    public function test_database_backup_command_succeeds_on_sqlite(): void
    {
        $exitCode = Artisan::call('db:backup');

        $this->assertSame(0, $exitCode);
        $this->assertNotEmpty(File::files(config('security.backup.path')));
    }

    public function test_security_headers_middleware_applied(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_maintenance_backup_route_works(): void
    {
        $this->actingAs($this->admin)
            ->post(route('security.maintenance.backup'))
            ->assertRedirect()
            ->assertSessionHas('success');
    }
}
