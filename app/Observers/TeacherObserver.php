<?php

namespace App\Observers;

use App\Models\Teacher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TeacherObserver
{
    /**
     * Handle the Teacher "saved" event.
     */
    public function saved(Teacher $teacher): void
    {
        if (app()->environment('testing')) {
            return;
        }
        $user = $teacher->user_id ? \App\Models\User::find($teacher->user_id) : null;
        $username = $user ? $user->username : ($teacher->nip ?? $teacher->email);

        if (!$username) {
            return;
        }

        $subjects = $teacher->subject 
            ? array_map('trim', explode(',', $teacher->subject))
            : [];

        $payload = [
            'username' => $username,
            'name' => $teacher->name,
            'role' => 'guru',
            'tanggal_lahir' => $teacher->dob ? $teacher->dob->format('Y-m-d') : null,
            'subjects' => $subjects,
            'email' => $teacher->email ?? $user?->email,
            'password' => $user?->password,
        ];

        try {
            $apiUrl = config('services.cbt.api_url');
            $secretKey = config('services.cbt.sync_secret_key');

            if ($apiUrl && $secretKey) {
                Http::timeout(5)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post(rtrim($apiUrl, '/') . '/sync/user', $payload);
            }
        } catch (\Exception $e) {
            Log::error('CBT Teacher Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Teacher "deleted" event.
     */
    public function deleted(Teacher $teacher): void
    {
        if (app()->environment('testing')) {
            return;
        }
        $user = $teacher->user_id ? \App\Models\User::find($teacher->user_id) : null;
        $username = $user ? $user->username : ($teacher->nip ?? $teacher->email);

        if (!$username) {
            return;
        }

        $payload = [
            'username' => $username,
        ];

        try {
            $apiUrl = config('services.cbt.api_url');
            $secretKey = config('services.cbt.sync_secret_key');

            if ($apiUrl && $secretKey) {
                Http::timeout(5)
                    ->withHeaders(['X-Sync-Secret' => $secretKey])
                    ->post(rtrim($apiUrl, '/') . '/sync/user/delete', $payload);
            }
        } catch (\Exception $e) {
            Log::error('CBT Teacher Delete Sync failed: ' . $e->getMessage());
        }
    }
}
