<?php

namespace App\Observers;

use App\Models\Guardian;

class GuardianObserver
{
    /**
     * Handle the Guardian "created" event.
     */
    public function created(Guardian $guardian): void
    {
        $user = $guardian->user;

        if (! $user || $user->hasRole('ولي أمر')) {
            return;
        }

        $user->assignRole('ولي أمر');
    }
}
