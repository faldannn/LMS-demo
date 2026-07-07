<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use App\Policies\Concerns\ManagesClassSubjectAccess;

class QuizPolicy
{
    use ManagesClassSubjectAccess;

    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Quiz $quiz): bool
    {
        $quiz->loadMissing('classSubject');

        if ($user->isStaff()) {
            return $this->canManageClassSubject($user, $quiz->classSubject);
        }

        return $user->isSiswa()
            && $quiz->is_published
            && (int) $quiz->classSubject->class_id === (int) $user->student?->class_id;
    }

    public function attempt(User $user, Quiz $quiz): bool
    {
        return $this->view($user, $quiz);
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, Quiz $quiz): bool
    {
        $quiz->loadMissing('classSubject');

        return $this->canManageClassSubject($user, $quiz->classSubject);
    }

    public function delete(User $user, Quiz $quiz): bool
    {
        return $this->update($user, $quiz);
    }
}
