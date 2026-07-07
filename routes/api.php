<?php

use App\Http\Controllers\Api\SelfAttendanceApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'check.active', 'throttle:' . config('security.rate_limits.api_self_attendance', '30,1')])
    ->prefix('attendance/self')->name('api.attendance.self.')->group(function () {
    Route::get('config', [SelfAttendanceApiController::class, 'config'])->name('config');
    Route::post('face-profile', [SelfAttendanceApiController::class, 'saveFaceProfile'])->name('face-profile');
    Route::post('check-in', [SelfAttendanceApiController::class, 'checkIn'])->name('check-in');
    Route::get('history', [SelfAttendanceApiController::class, 'history'])->name('history');
});

