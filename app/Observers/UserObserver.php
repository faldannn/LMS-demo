<?php

namespace App\Observers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function saved(User $user): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $roleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        if ($user->wasRecentlyCreated && in_array($roleValue, ['siswa', 'guru'])) {
            return;
        }

        if ($roleValue === 'siswa') {
            $user->loadMissing('student');
            $student = $user->student;
            if ($student) {
                if (!$student->relationLoaded('class')) {
                    $student->load('class');
                }
                $payload = [
                    'nis' => $student->nis,
                    'nama' => $student->name,
                    'kelas' => $student->class?->name,
                    'ruangan' => $student->class?->room,
                    'tanggal_lahir' => $student->dob ? $student->dob->format('Y-m-d') : null,
                    'password' => $user->password,
                ];
                try {
                    $apiUrl = config('services.cbt.api_url');
                    $secretKey = config('services.cbt.sync_secret_key');
                    if ($apiUrl && $secretKey) {
                        Http::timeout(5)
                            ->withHeaders(['X-Sync-Secret' => $secretKey])
                            ->post(rtrim($apiUrl, '/') . '/sync/student', $payload);
                    }
                } catch (\Exception $e) {
                    Log::error('CBT Student Sync via UserObserver failed: ' . $e->getMessage());
                }
            }
            return;
        }

        if ($roleValue === 'guru') {
            $user->loadMissing('teacher');
            $teacher = $user->teacher;
            if ($teacher) {
                $subjects = $teacher->subject 
                    ? array_map('trim', explode(',', $teacher->subject))
                    : [];
                $payload = [
                    'username' => $user->username,
                    'name' => $teacher->name,
                    'role' => 'guru',
                    'tanggal_lahir' => $teacher->dob ? $teacher->dob->format('Y-m-d') : null,
                    'subjects' => $subjects,
                    'email' => $teacher->email ?? $user->email,
                    'password' => $user->password,
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
                    Log::error('CBT Teacher Sync via UserObserver failed: ' . $e->getMessage());
                }
            }
            return;
        }

        $cbtRole = match ($roleValue) {
            'superadmin' => 'admin',
            'admin', 'kepala_sekolah' => 'admin',
            'guru' => 'guru',
            default => 'pengawas',
        };

        $payload = [
            'username' => $user->username,
            'name' => $user->name,
            'role' => $cbtRole,
            'email' => $user->email,
            'password' => $user->password,
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
            Log::error('CBT User Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $roleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        // Students and teachers have their own observers, so we only handle admin/staff here
        if ($roleValue === 'siswa' || $roleValue === 'guru') {
            return;
        }

        $payload = [
            'username' => $user->username,
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
            Log::error('CBT User Delete Sync failed: ' . $e->getMessage());
        }
    }
}
