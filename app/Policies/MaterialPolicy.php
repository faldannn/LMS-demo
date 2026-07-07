<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\User;
use App\Policies\Concerns\ManagesClassSubjectAccess;

class MaterialPolicy
{
    use ManagesClassSubjectAccess;

    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Material $material): bool
    {
        $material->loadMissing('classSubject');

        if ($user->isStaff()) {
            return $this->canManageClassSubject($user, $material->classSubject)
                || $user->isKepalaSekolah();
        }

        return $user->isSiswa()
            && $material->is_published
            && (int) $material->classSubject->class_id === (int) $user->student?->class_id;
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, Material $material): bool
    {
        $material->loadMissing('classSubject');

        return $this->canManageClassSubject($user, $material->classSubject);
    }

    public function delete(User $user, Material $material): bool
    {
        return $this->update($user, $material);
    }
}
