<?php

use App\Http\Controllers\Api\RiansaKartuWebhookController;
use App\Http\Controllers\Api\TapAttendanceController;
use Illuminate\Support\Facades\Route;

// Universal Tap Attendance API
Route::post('/v1/attendance/tap', [TapAttendanceController::class, 'handle'])->name('api.v1.attendance.tap');
Route::get('/v1/attendance/tap/status', [TapAttendanceController::class, 'status'])->name('api.v1.attendance.tap.status');

// Alias routes for convenience
Route::post('/attendance/tap', [TapAttendanceController::class, 'handle'])->name('api.attendance.tap');
Route::get('/attendance/tap/status', [TapAttendanceController::class, 'status'])->name('api.attendance.tap.status');

// Riansa Kartu RFID Attendance Webhook Route
Route::post('/v1/kartu/webhook', [RiansaKartuWebhookController::class, 'handle'])->name('api.v1.kartu.webhook');
Route::post('/kartu/webhook', [RiansaKartuWebhookController::class, 'handle'])->name('api.kartu.webhook');
