<?php

namespace App\Observers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "saved" event.
     */
    public function saved(User $user): void
    {
        $roleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        // Students are synced via StudentObserver, teachers via TeacherObserver, so we only sync admin/staff here
        if ($roleValue === 'siswa' || $roleValue === 'guru') {
            return;
        }

        $cbtRole = match ($roleValue) {
            'kepala_sekolah' => 'admin',
            'guru' => 'guru',
            default => 'pengawas',
        };

        $payload = [
            'username' => $user->username,
            'name' => $user->name,
            'role' => $cbtRole,
        ];

        try {
            $apiUrl = env('CBT_API_URL');
            $secretKey = env('SYNC_SECRET_KEY');

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
        $roleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        // Students and teachers have their own observers, so we only handle admin/staff here
        if ($roleValue === 'siswa' || $roleValue === 'guru') {
            return;
        }

        $payload = [
            'username' => $user->username,
        ];

        try {
            $apiUrl = env('CBT_API_URL');
            $secretKey = env('SYNC_SECRET_KEY');

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
