<?php

namespace App\Policies;

use App\Models\QuestionBank;
use App\Models\User;
use App\Policies\Concerns\ManagesClassSubjectAccess;

class QuestionBankPolicy
{
    use ManagesClassSubjectAccess;

    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, QuestionBank $bank): bool
    {
        $bank->loadMissing('classSubject');

        return $this->canManageClassSubject($user, $bank->classSubject) || $user->isKepalaSekolah();
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, QuestionBank $bank): bool
    {
        return $this->view($user, $bank);
    }

    public function delete(User $user, QuestionBank $bank): bool
    {
        return $this->view($user, $bank);
    }
}
