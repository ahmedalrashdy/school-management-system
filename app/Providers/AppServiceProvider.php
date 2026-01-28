<?php

namespace App\Providers;

use App\Enums\PermissionEnum;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Observers\GuardianObserver;
use App\Observers\StudentObserver;
use App\Observers\TeacherObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Support\GuestWriteBlocker;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GuestWriteBlocker::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');

        // Super Admin bypass: Grant all permissions to users with is_admin = true
        Gate::before(function ($user, $ability) {
            if ($user->is_admin ?? false) {
                return true;
            }
        });

        // Register Model Observers
        User::observe(UserObserver::class);
        Teacher::observe(TeacherObserver::class);
        Student::observe(StudentObserver::class);
        Guardian::observe(GuardianObserver::class);

        $this->registerGuestWriteGuards();

        if (class_exists(PermissionEnum::class)) {
            class_alias(PermissionEnum::class, 'Perm');
        }

    }

    private function registerGuestWriteGuards(): void
    {
        $events = ['creating', 'updating', 'deleting', 'restoring', 'forceDeleting'];

        foreach ($events as $event) {
            Event::listen("eloquent.{$event}: *", function (string $eventName, array $data) {
                if (app()->runningInConsole()) {
                    return;
                }

                $user = Auth::user();

                if (! $user || ! $user->is_guest) {
                    return;
                }

                app(GuestWriteBlocker::class)->block('أنت في وضع الزائر ولا يمكنك تنفيذ عمليات على البيانات.');

                return false;
            });
        }
    }
}
