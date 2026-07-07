<?php

namespace App\Policies;

use App\Models\AcademicCalendarEvent;
use App\Models\User;

class AcademicCalendarEventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, AcademicCalendarEvent $event): bool
    {
        return $user->isKepalaSekolah() || $user->id === $event->created_by;
    }

    public function delete(User $user, AcademicCalendarEvent $event): bool
    {
        return $this->update($user, $event);
    }
}
