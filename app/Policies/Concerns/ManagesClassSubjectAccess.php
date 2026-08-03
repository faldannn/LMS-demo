<?php

namespace App\Policies\Concerns;

use App\Enums\UserRole;
use App\Models\ClassSubject;
use App\Models\User;

trait ManagesClassSubjectAccess
{
    protected function canManageClassSubject(User $user, ClassSubject $classSubject): bool
    {
        if ($user->isKepalaSekolah() || $user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        if ($user->isGuru()) {
            $teacher = $user->teacher;
            if (!$teacher) {
                return false;
            }
            if ((int) $classSubject->teacher_id === (int) $teacher->id) {
                return true;
            }
            return $teacher->schedules()->where('class_id', $classSubject->class_id)->exists()
                || $teacher->homeroomClasses()->where('id', $classSubject->class_id)->exists();
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
