<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function view(User $user, Announcement $announcement): bool
    {
        if (!$announcement->is_published) {
            return false;
        }

        if ($user->isGuru()) {
            return $announcement->created_by === $user->id || $announcement->type === 'umum';
        }

        return $user->isSiswa()
            && ($announcement->type === 'umum'
                || (int) $announcement->class_id === (int) $user->student?->class_id);
    }
}
