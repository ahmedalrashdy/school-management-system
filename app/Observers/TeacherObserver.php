<?php

namespace App\Observers;

use App\Models\Teacher;

class TeacherObserver
{
    /**
     * Handle the Teacher "created" event.
     */
    public function created(Teacher $teacher): void
    {
        $user = $teacher->user;

        if (! $user || $user->hasRole('مدرس')) {
            return;
        }

        $user->assignRole('مدرس');
    }
}
