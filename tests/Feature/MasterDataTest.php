<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->kepalaSekolah()->create(['status' => true]);
    }

    public function test_kepala_sekolah_can_create_major(): void
    {
        $this->actingAs($this->admin)
            ->post(route('majors.store'), [
                'code' => 'RPL',
                'name' => 'Rekayasa Perangkat Lunak',
                'status' => true,
            ])
            ->assertRedirect(route('majors.index'));

        $this->assertDatabaseHas('majors', ['code' => 'RPL']);
    }

    public function test_kepala_sekolah_can_create_academic_year_and_semester(): void
    {
        $this->actingAs($this->admin)
            ->post(route('academic-years.store'), [
                'year' => '2025/2026',
                'description' => 'TA baru',
                'is_active' => true,
            ])
            ->assertRedirect(route('academic-years.index'));

        $year = AcademicYear::where('year', '2025/2026')->first();
        $this->assertNotNull($year);

        $this->actingAs($this->admin)
            ->post(route('semesters.store'), [
                'academic_year_id' => $year->id,
                'number' => 1,
                'start_date' => '2025-07-01',
                'end_date' => '2025-12-20',
                'is_active' => true,
            ])
            ->assertRedirect(route('semesters.index'));

        $this->assertDatabaseHas('semesters', ['academic_year_id' => $year->id, 'number' => 1]);
    }

    public function test_soft_delete_and_restore_major(): void
    {
        $major = Major::create(['code' => 'IPS', 'name' => 'IPS', 'status' => true]);

        $this->actingAs($this->admin)
            ->delete(route('majors.destroy', $major))
            ->assertRedirect(route('majors.index'));

        $this->assertSoftDeleted('majors', ['id' => $major->id]);

        $this->actingAs($this->admin)
            ->post(route('majors.restore', $major->id))
            ->assertRedirect(route('majors.index'));

        $this->assertDatabaseHas('majors', ['id' => $major->id, 'deleted_at' => null]);
    }

    public function test_guru_cannot_access_master_data(): void
    {
        $guru = User::factory()->guru()->create(['status' => true]);

        $this->actingAs($guru)->get(route('majors.index'))->assertForbidden();
        $this->actingAs($guru)->get(route('academic-years.index'))->assertForbidden();
        $this->actingAs($guru)->get(route('semesters.index'))->assertForbidden();
    }
}
