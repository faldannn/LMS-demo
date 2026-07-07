<?php

namespace App\Services;

class GeofenceService
{
    private const EARTH_RADIUS_METERS = 6371000;

    /**
     * Haversine distance in meters between two coordinates.
     */
    public function distanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }

    public function isWithinRadius(float $lat, float $lon, ?float $centerLat = null, ?float $centerLon = null, ?int $radiusMeters = null): array
    {
        $centerLat ??= (float) config('attendance.geofence.latitude');
        $centerLon ??= (float) config('attendance.geofence.longitude');
        $radiusMeters ??= (int) config('attendance.geofence.radius_meters');

        $distance = $this->distanceMeters($lat, $lon, $centerLat, $centerLon);

        return [
            'distance_meters' => round($distance, 2),
            'within_radius' => $distance <= $radiusMeters,
            'radius_meters' => $radiusMeters,
            'center_latitude' => $centerLat,
            'center_longitude' => $centerLon,
        ];
    }

    public function getConfig(): array
    {
        return [
            'latitude' => (float) config('attendance.geofence.latitude'),
            'longitude' => (float) config('attendance.geofence.longitude'),
            'radius_meters' => (int) config('attendance.geofence.radius_meters'),
            'detection_min' => (float) config('attendance.face.detection_min_confidence'),
            'match_min' => (float) config('attendance.face.match_min_confidence'),
            'allow_camera_fallback' => (bool) config('attendance.self_checkin.allow_camera_fallback'),
            'allow_gps_fallback' => (bool) config('attendance.self_checkin.allow_gps_fallback'),
            'allow_manual_fallback' => (bool) config('attendance.self_checkin.allow_manual_fallback'),
        ];
    }
}
