<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use App\Policies\Concerns\ManagesClassSubjectAccess;

class AssignmentPolicy
{
    use ManagesClassSubjectAccess;

    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Assignment $assignment): bool
    {
        $assignment->loadMissing('classSubject');

        if ($user->isStaff()) {
            return $this->canManageClassSubject($user, $assignment->classSubject);
        }

        return $user->isSiswa()
            && $assignment->is_published
            && (int) $assignment->classSubject->class_id === (int) $user->student?->class_id;
    }

    public function submit(User $user, Assignment $assignment): bool
    {
        return $this->view($user, $assignment);
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, Assignment $assignment): bool
    {
        $assignment->loadMissing('classSubject');

        return $this->canManageClassSubject($user, $assignment->classSubject);
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $this->update($user, $assignment);
    }

    public function grade(User $user, Assignment $assignment): bool
    {
        return $this->update($user, $assignment);
    }
}
