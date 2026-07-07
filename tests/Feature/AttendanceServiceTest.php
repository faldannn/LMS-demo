<?php

namespace Tests\Unit;

use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_today_summary_returns_correct_structure(): void
    {
        $service = new AttendanceService();
        $summary = $service->getTodaySummary();

        $this->assertArrayHasKey('hadir', $summary);
        $this->assertArrayHasKey('alfa', $summary);
        $this->assertArrayHasKey('percentage', $summary);
        $this->assertIsFloat($summary['percentage']);
    }

    public function test_monthly_trend_returns_12_months(): void
    {
        $service = new AttendanceService();
        $trend = $service->getMonthlyTrend(2025);

        $this->assertCount(12, $trend);
        $this->assertEquals(1, $trend[0]['month']);
        $this->assertEquals(12, $trend[11]['month']);
    }
}