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
        if (app()->environment('testing')) {
            return;
        }
        if (!$student->relationLoaded('class')) {
            $student->load('class');
        }

        if (!$student->relationLoaded('user')) {
            $student->load('user');
        }
        $user = $student->user;
        $payload = [
            'nis' => $student->nis,
            'nama' => $student->name,
            'kelas' => $student->class?->name,
            'ruangan' => $student->class?->room,
            'tanggal_lahir' => $student->dob ? $student->dob->format('Y-m-d') : null,
            'password' => $user?->password,
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
        if (app()->environment('testing')) {
            return;
        }
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
