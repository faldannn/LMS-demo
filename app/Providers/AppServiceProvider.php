<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use App\Enums\UserRole;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // A class normally shares one public IP address.  Do not let ten
        // different students signing in at once exhaust one shared limit.
        // Failed-password protection remains in AuthService and is keyed to
        // the individual account.
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(30)
            ->by(strtolower((string) $request->input('username')).'|'.$request->ip()));

        \App\Models\Student::observe(\App\Observers\StudentObserver::class);
        \App\Models\Teacher::observe(\App\Observers\TeacherObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Subject::observe(\App\Observers\SubjectObserver::class);
        \App\Models\Classes::observe(\App\Observers\ClassObserver::class);

        if (!$this->app->environment('production')) {
            \Illuminate\Database\Eloquent\Model::preventLazyLoading();
            \Illuminate\Database\Eloquent\Model::preventSilentlyDiscardingAttributes();
        }

        Gate::before(function ($user, $ability) {
            if ($user->hasRole(['admin', 'superadmin'])) {
                return true;
            }
        });

        Gate::define('manage-admin', function ($user) {
            return $user->isAdmin() || $user->isSuperAdmin();
        });

        Gate::define('access-elearning', function ($user) {
            return true; // All authenticated users (Admin, Guru, Siswa)
        });

        Gate::define('access-staff', function ($user) {
            return $user->isStaff();
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
            $settings = Cache::remember('app.settings', 3600, function () {
                return Setting::query()->pluck('value', 'key')->toArray();
            });

            if (empty($settings)) {
                return;
            }

            config([
                'school.name' => $settings['school_name'] ?? config('school.name'),
                'school.system_name' => $settings['school_name'] ?? config('school.system_name'),
                'school.system_short' => $settings['school_name'] ?? config('school.system_short'),
                'school.address' => $settings['school_address'] ?? config('school.address'),
                'school.phone' => $settings['school_phone'] ?? config('school.phone'),
                'school.email' => $settings['school_email'] ?? config('school.email'),
                'school.principal' => $settings['school_principal'] ?? config('school.principal'),

                'attendance.student_checkin_start' => $settings['att_student_checkin_start'] ?? config('attendance.student_checkin_start', '06:00:00'),
                'attendance.late_limit' => $settings['att_late_limit'] ?? config('attendance.late_limit', '07:15:00'),
                'attendance.student_checkout_start' => $settings['att_student_checkout_start'] ?? config('attendance.student_checkout_start', '14:00:00'),
                'attendance.student_checkout_end' => $settings['att_student_checkout_end'] ?? config('attendance.student_checkout_end', '17:00:00'),

                'attendance.student_checkin_start_siang' => $settings['att_student_checkin_start_siang'] ?? config('attendance.student_checkin_start_siang', '11:30:00'),
                'attendance.late_limit_siang' => $settings['att_late_limit_siang'] ?? config('attendance.late_limit_siang', '13:00:00'),
                'attendance.student_checkout_start_siang' => $settings['att_student_checkout_start_siang'] ?? config('attendance.student_checkout_start_siang', '17:30:00'),
                'attendance.student_checkout_end_siang' => $settings['att_student_checkout_end_siang'] ?? config('attendance.student_checkout_end_siang', '20:00:00'),

                'attendance.teacher_checkin_start' => $settings['att_teacher_checkin_start'] ?? config('attendance.teacher_checkin_start', '06:00:00'),
                'attendance.teacher_late_limit' => $settings['att_teacher_late_limit'] ?? config('attendance.teacher_late_limit', '07:15:00'),
                'attendance.teacher_checkout_start' => $settings['att_teacher_checkout_start'] ?? config('attendance.teacher_checkout_start', '15:00:00'),
                'attendance.teacher_checkout_end' => $settings['att_teacher_checkout_end'] ?? config('attendance.teacher_checkout_end', '18:00:00'),

                'attendance.start_time' => $settings['att_start_time'] ?? config('attendance.start_time'),
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
