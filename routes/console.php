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

    // Shared hosting does not keep a queue worker daemon alive. Process queued
    // notifications and CBT sync jobs during each scheduler invocation instead.
    $schedule->command('queue:work database --queue=default,notifications --stop-when-empty --max-time=45 --sleep=1 --tries=3')
        ->everyMinute()
        ->withoutOverlapping();

    // Cleanup expired sessions and stale cache tags
    $schedule->command('session:gc')->dailyAt('01:00');
    $schedule->command('cache:prune-stale-tags')->weekly();
});
