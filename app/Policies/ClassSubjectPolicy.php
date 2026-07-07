<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ClassSubject;
use App\Models\User;
use App\Policies\Concerns\ManagesClassSubjectAccess;

class ClassSubjectPolicy
{
    use ManagesClassSubjectAccess;

    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::KepalaSekolah, UserRole::Guru, UserRole::Siswa], true);
    }

    public function view(User $user, ClassSubject $classSubject): bool
    {
        return $this->canViewClassSubject($user, $classSubject);
    }

    public function manage(User $user, ClassSubject $classSubject): bool
    {
        return $this->canManageClassSubject($user, $classSubject);
    }
}
