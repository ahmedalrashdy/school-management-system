<?php

namespace App\Observers;

use App\Mail\WelcomeParentMail;
use App\Mail\WelcomeStudentMail;
use App\Mail\WelcomeTeacherMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // التحقق من الشروط: يجب أن يكون المستخدم نشطاً ويطلب إعادة تعيين كلمة المرور
        if (! $user->is_active || ! $user->reset_password_required || ! $user->email) {
            return;
        }

        dispatch(function () use ($user) {
            // إعادة تحميل المستخدم لضمان الحصول على أحدث البيانات والأدوار
            $user->refresh();

            if ($user->hasRole('طالب')) {
                Mail::send(new WelcomeStudentMail($user));
            } elseif ($user->hasRole('ولي أمر')) {
                Mail::send(new WelcomeParentMail($user));
            } elseif ($user->hasRole('مدرس')) {
                Mail::send(new WelcomeTeacherMail($user));
            }
        })->afterResponse();
    }

    /**
     * Handle the User "updated" event.
     */
    public function updating(User $user): void
    {
        if ($user->isDirty('email')) {
            $user->reset_password_required = true;
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
