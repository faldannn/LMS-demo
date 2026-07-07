<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app()->booted(function () {
    $schedule = app(Schedule::class);

    $schedule->command('db:backup')->dailyAt('02:00')->withoutOverlapping();
    $schedule->command('logs:prune')->weeklyOn(0, '03:00');
    $schedule->command('queue:prune-failed --hours=168')->weekly();
    
    // Cleanup expired sessions and stale cache tags
    $schedule->command('session:gc')->dailyAt('01:00');
    $schedule->command('cache:prune-stale-tags')->weekly();
});
