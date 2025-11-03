<?php

use App\Http\Controllers\Common\AutocompleteController;
use App\Http\Controllers\Common\NotificationController;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Spatie\Activitylog\Models\Activity;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{notification}', [NotificationController::class, 'markAsReadAndRedirect'])->name('show');
    Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'is_active'])->prefix('common')->name('common.')
    ->group(function () {
        Route::get('autocomplete', AutocompleteController::class)->name('autocomplete');
    });

Route::get('test2', function () {
    $user = User::whereHas('guardian')->first();
    $user->password = Hash::make('12345678');
    $user->reset_password_required = false;
    $user->is_active = true;
    $user->givePermissionTo(\Perm::AccessAdminPanel);
    $user->assignRole('مدرس');
    $user->save();
    dd($user->toArray());
    echo $user->isDirty() ? 'dirty' : 'not dirty';
    echo $user->wasChanged() ? 'wasChanged' : 'not wasChanged';

    return 'done';
});
Route::get('test', function () {

    return view("test");
});

require __DIR__ . '/auth.php';
