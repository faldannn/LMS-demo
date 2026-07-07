<?php

namespace App\Policies\Concerns;

use App\Enums\UserRole;
use App\Models\ClassSubject;
use App\Models\User;

trait ManagesClassSubjectAccess
{
    protected function canManageClassSubject(User $user, ClassSubject $classSubject): bool
    {
        if ($user->isKepalaSekolah()) {
            return true;
        }

        if ($user->isGuru()) {
            return (int) $classSubject->teacher_id === (int) $user->teacher?->id;
        }

        return false;
    }

    protected function canViewClassSubject(User $user, ClassSubject $classSubject): bool
    {
        if ($this->canManageClassSubject($user, $classSubject)) {
            return true;
        }

        if ($user->isSiswa()) {
            return (int) $classSubject->class_id === (int) $user->student?->class_id;
        }

        return false;
    }
}
