<?php

namespace App\Observers;

use App\Models\Classes;

class ClassObserver
{
    /**
     * Handle the Classes "saved" event.
     */
    public function saved(Classes $class): void
    {
        if ($class->wasChanged('room') || $class->wasRecentlyCreated) {
            $studentObserver = new StudentObserver();
            if (!$class->relationLoaded('students')) {
                $class->load('students');
            }
            foreach ($class->students as $student) {
                $studentObserver->saved($student);
            }
        }
    }
}
