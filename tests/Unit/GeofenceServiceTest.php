<?php

namespace Tests\Unit;

use App\Services\GeofenceService;
use Tests\TestCase;

class GeofenceServiceTest extends TestCase
{
    public function test_haversine_distance_same_point_is_zero(): void
    {
        $service = new GeofenceService();
        $this->assertEquals(0, $service->distanceMeters(-6.2349, 106.9896, -6.2349, 106.9896));
    }

    public function test_within_radius_when_inside(): void
    {
        config(['attendance.geofence.latitude' => -6.2349]);
        config(['attendance.geofence.longitude' => 106.9896]);
        config(['attendance.geofence.radius_meters' => 200]);

        $service = new GeofenceService();
        $result = $service->isWithinRadius(-6.2349, 106.9896);

        $this->assertTrue($result['within_radius']);
        $this->assertLessThan(1, $result['distance_meters']);
    }

    public function test_outside_radius_when_far(): void
    {
        config(['attendance.geofence.latitude' => -6.2349]);
        config(['attendance.geofence.longitude' => 106.9896]);
        config(['attendance.geofence.radius_meters' => 100]);

        $service = new GeofenceService();
        $result = $service->isWithinRadius(-6.3, 107.1);

        $this->assertFalse($result['within_radius']);
        $this->assertGreaterThan(100, $result['distance_meters']);
    }
}
