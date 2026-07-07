<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isKepalaSekolah();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isKepalaSekolah();
    }

    public function create(User $user): bool
    {
        return $user->isKepalaSekolah();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isKepalaSekolah();
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isKepalaSekolah()
            && $user->id !== $model->id;
    }
}
