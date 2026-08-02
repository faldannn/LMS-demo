<?php

use Illuminate\Support\Facades\Route;

// Riansa Kartu RFID Attendance Webhook Route
Route::post('/v1/kartu/webhook', [\App\Http\Controllers\Api\RiansaKartuWebhookController::class, 'handle'])->name('api.v1.kartu.webhook');
Route::post('/kartu/webhook', [\App\Http\Controllers\Api\RiansaKartuWebhookController::class, 'handle'])->name('api.kartu.webhook');

