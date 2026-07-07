<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Enums\UserRole;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if (!$this->app->environment('production')) {
            \Illuminate\Database\Eloquent\Model::preventLazyLoading();
            \Illuminate\Database\Eloquent\Model::preventSilentlyDiscardingAttributes();
        }

        Gate::before(function ($user, $ability) {
            if ($user->hasRole(UserRole::KepalaSekolah->value)) {
                return true;
            }
        });

        Gate::define('manage-admin', function ($user) {
            return $user->isKepalaSekolah();
        });

        Gate::define('access-elearning', function ($user) {
            return true; // All authenticated users (Admin, Guru, Siswa)
        });

        Gate::define('access-siswa', function ($user) {
            return $user->isSiswa();
        });

        if ($this->app->environment('production') || str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Blade::directive('active', function ($expression) {
            return "<?php
                \$__activeArgs = [{$expression}];
                if (count(\$__activeArgs) === 3) {
                    echo request()->routeIs(\$__activeArgs[0]) ? \$__activeArgs[1] : \$__activeArgs[2];
                } else {
                    echo request()->routeIs(\$__activeArgs[0]) ? 'active' : '';
                }
            ?>";
        });

        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole(array_map('trim', explode(',', {$expression})))): ?>";
        });
        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        \Carbon\Carbon::setLocale('id');

        if (!$this->app->runningInConsole()) {
            $this->loadRuntimeSettings();
            view()->share('schoolConfig', config('school'));
            view()->share('brandConfig', config('school.brand'));
        }
    }

    private function loadRuntimeSettings(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            $settings = Cache::remember('app.settings', 3600, function () {
                return Setting::query()->pluck('value', 'key')->toArray();
            });

            config([
                'school.name' => $settings['school_name'] ?? config('school.name'),
                'school.address' => $settings['school_address'] ?? config('school.address'),
                'school.phone' => $settings['school_phone'] ?? config('school.phone'),
                'school.email' => $settings['school_email'] ?? config('school.email'),
                'school.principal' => $settings['school_principal'] ?? config('school.principal'),

                'attendance.start_time' => $settings['att_start_time'] ?? config('attendance.start_time'),
                'attendance.late_limit' => $settings['att_late_limit'] ?? config('attendance.late_limit'),
                'attendance.end_time' => $settings['att_end_time'] ?? config('attendance.end_time'),
                'attendance.min_percentage' => $settings['att_min_pct'] ?? config('attendance.min_percentage'),
                'attendance.working_days' => $settings['att_working_days'] ?? config('attendance.working_days'),

                'attendance.geofence.latitude' => isset($settings['att_geofence_lat']) ? (float) $settings['att_geofence_lat'] : config('attendance.geofence.latitude'),
                'attendance.geofence.longitude' => isset($settings['att_geofence_lng']) ? (float) $settings['att_geofence_lng'] : config('attendance.geofence.longitude'),
                'attendance.geofence.radius_meters' => isset($settings['att_geofence_radius']) ? (int) $settings['att_geofence_radius'] : config('attendance.geofence.radius_meters'),
                'attendance.face.detection_min_confidence' => isset($settings['att_face_detection_min']) ? (float) $settings['att_face_detection_min'] : config('attendance.face.detection_min_confidence'),
                'attendance.face.match_min_confidence' => isset($settings['att_face_match_min']) ? (float) $settings['att_face_match_min'] : config('attendance.face.match_min_confidence'),
            ]);
        } catch (\Exception $e) {
            // Database unavailable during initial setup
        }
    }
}

