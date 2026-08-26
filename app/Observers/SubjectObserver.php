<?php

namespace App\Observers;

use App\Models\Subject;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubjectObserver
{
    /**
     * Handle the Subject "saved" event.
     */
    public function saved(Subject $subject): void
    {
        if (app()->environment('testing')) {
            return;
        }
        $payload = [
            'id' => $subject->id,
            'name' => $subject->name,
            'code' => $subject->code,
            'level' => $subject->grade,
        ];

        try {
            $apiUrl = config('services.cbt.api_url');
            $secretKey = config('services.cbt.sync_secret_key');

            if ($apiUrl && $secretKey) {
                Http::timeout(5)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post(rtrim($apiUrl, '/') . '/sync/subject', $payload);
            }
        } catch (\Exception $e) {
            Log::error('CBT Subject Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Subject "deleted" event.
     */
    public function deleted(Subject $subject): void
    {
        if (app()->environment('testing')) {
            return;
        }
        $payload = [
            'id' => $subject->id,
        ];

        try {
            $apiUrl = config('services.cbt.api_url');
            $secretKey = config('services.cbt.sync_secret_key');

            if ($apiUrl && $secretKey) {
                Http::timeout(5)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post(rtrim($apiUrl, '/') . '/sync/subject/delete', $payload);
            }
        } catch (\Exception $e) {
            Log::error('CBT Subject Delete Sync failed: ' . $e->getMessage());
        }
    }
}
