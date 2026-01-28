<?php

namespace App\Observers;

use App\Models\Student;

class StudentObserver
{
    /**
     * Handle the Student "created" event.
     */
    public function created(Student $student): void
    {
        $user = $student->user;

        if (! $user || $user->hasRole('طالب')) {
            return;
        }

        $user->assignRole('طالب');
    }
}
