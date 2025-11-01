<?php

namespace App\Providers;

use App\Enums\PermissionEnum;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

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

        // Register User Observer
        User::observe(UserObserver::class);

        if (class_exists(PermissionEnum::class)) {
            class_alias(PermissionEnum::class, 'Perm');
        }

    }
}
