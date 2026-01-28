<?php

use App\Http\Middleware\EnsureResetPasswordNotRequired;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\HandleGuestWriteBlocked;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth', 'is_active', 'reset_password_required', 'can:' . \Perm::AccessAdminPanel->value])
                ->prefix('dashboard')
                ->name('dashboard.')
                ->group(base_path('routes/dashboard.php'));

            Route::middleware(['web', 'auth', 'is_active', 'reset_password_required'])
                ->prefix('portal')
                ->name('portal.')
                ->group(base_path('routes/portal.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', HandleGuestWriteBlocked::class);
        $middleware->alias([
            'is_active' => EnsureUserIsActive::class,
            'reset_password_required' => EnsureResetPasswordNotRequired::class,
            // laravel spatia permissions
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
