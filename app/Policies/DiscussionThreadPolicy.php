<?php

namespace App\Policies;

use App\Models\DiscussionThread;
use App\Models\User;
use App\Policies\Concerns\ManagesClassSubjectAccess;

class DiscussionThreadPolicy
{
    use ManagesClassSubjectAccess;

    public function viewAny(User $user): bool
    {
        return auth()->check();
    }

    public function view(User $user, DiscussionThread $thread): bool
    {
        $thread->loadMissing('classSubject');

        if ($user->isStaff()) {
            return $this->canManageClassSubject($user, $thread->classSubject) || $user->isKepalaSekolah();
        }

        return $user->isSiswa()
            && (int) $thread->classSubject->class_id === (int) $user->student?->class_id;
    }

    public function create(User $user): bool
    {
        return auth()->check();
    }

    public function reply(User $user, DiscussionThread $thread): bool
    {
        return $this->view($user, $thread) && !$thread->is_locked;
    }
}
