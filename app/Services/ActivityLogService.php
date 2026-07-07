<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function log(
        string $action,
        ?User $user = null,
        ?Model $subject = null,
        ?string $description = null,
        array $properties = [],
        ?Request $request = null,
    ): ActivityLog {
        $request ??= request();

        return ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'properties' => $properties ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'logged_at' => now(),
        ]);
    }

    public function logLogin(User $user, Request $request): ActivityLog
    {
        return $this->log(
            action: 'login',
            user: $user,
            subject: $user,
            description: "User {$user->username} berhasil login",
            request: $request,
        );
    }

    public function logLogout(?User $user, Request $request): ?ActivityLog
    {
        if (!$user) {
            return null;
        }

        return $this->log(
            action: 'logout',
            user: $user,
            subject: $user,
            description: "User {$user->username} keluar dari sistem",
            request: $request,
        );
    }

    public function logCrud(string $action, Model $subject, ?string $description = null, array $properties = []): ActivityLog
    {
        return $this->log(
            action: $action,
            user: auth()->user(),
            subject: $subject,
            description: $description ?? class_basename($subject) . " #{$subject->getKey()} — {$action}",
            properties: $properties,
        );
    }

    public function logSettings(string $group, array $changedKeys = []): ActivityLog
    {
        return $this->log(
            action: 'settings_update',
            user: auth()->user(),
            description: "Pengaturan {$group} diperbarui",
            properties: ['keys' => $changedKeys],
        );
    }
}
