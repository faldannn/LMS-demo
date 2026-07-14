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
        $payload = [
            'id' => $subject->id,
            'name' => $subject->name,
            'code' => $subject->code,
            'level' => $subject->grade,
        ];

        try {
            $apiUrl = env('CBT_API_URL');
            $secretKey = env('SYNC_SECRET_KEY');

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
        $payload = [
            'id' => $subject->id,
        ];

        try {
            $apiUrl = env('CBT_API_URL');
            $secretKey = env('SYNC_SECRET_KEY');

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
