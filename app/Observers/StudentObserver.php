<?php

namespace App\Observers;

use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudentObserver
{
    /**
     * Handle the Student "saved" event.
     */
    public function saved(Student $student): void
    {
        if (!$student->relationLoaded('class')) {
            $student->load('class');
        }

        $payload = [
            'nis' => $student->nis,
            'nama' => $student->name,
            'kelas' => $student->class?->name,
            'tanggal_lahir' => $student->dob ? $student->dob->format('Y-m-d') : null,
        ];

        try {
            $apiUrl = env('CBT_API_URL');
            $secretKey = env('SYNC_SECRET_KEY');

            if ($apiUrl && $secretKey) {
                Http::timeout(5)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post(rtrim($apiUrl, '/') . '/sync/student', $payload);
            }
        } catch (\Exception $e) {
            Log::error('CBT Student Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Student "deleted" event.
     */
    public function deleted(Student $student): void
    {
        $payload = [
            'nis' => $student->nis,
        ];

        try {
            $apiUrl = env('CBT_API_URL');
            $secretKey = env('SYNC_SECRET_KEY');

            if ($apiUrl && $secretKey) {
                Http::timeout(5)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post(rtrim($apiUrl, '/') . '/sync/student/delete', $payload);
            }
        } catch (\Exception $e) {
            Log::error('CBT Student Delete Sync failed: ' . $e->getMessage());
        }
    }
}
