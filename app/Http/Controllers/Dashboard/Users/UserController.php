<?php

namespace App\Http\Controllers\Dashboard\Users;

use App\Enums\GenderEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Users\Users\ManageUserRolesRequest;
use App\Http\Requests\Dashboard\Users\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::UsersUpdate->value, only: ['edit', 'update']),
            new Middleware('can:' . \Perm::UsersManageRoles->value, only: ['manageRoles', 'updateRoles']),
        ];
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('dashboard.users.edit', [
            'user' => $user,
            'genders' => GenderEnum::options(),
            'isReadonly' => !$user->reset_password_required,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {

        // Check if user has activated their account (reset_password_required = false)
        // If so, make fields readonly
        if (!$user->reset_password_required) {
            return redirect()
                ->route('dashboard.users.index')
                ->with('error', 'لا يمكن تعديل بيانات المستخدم بعد تفعيل حسابه.');
        }

        $user->update($request->validated());

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }

    /**
     * Show the form for managing user roles.
     */
    public function manageRoles(User $user): View
    {
        $coreRoleNames = ['طالب', 'مدرس', 'ولي أمر'];

        $user->load('roles');

        return view('dashboard.users.manage-roles', [
            'user' => $user,
            'availableRoles' => Role::withCount('permissions')
                ->whereNotIn('name', $coreRoleNames)->latest()->get(),
            'currentRoles' => $user->roles,
            'coreRoles' => $user->roles->whereIn('name', $coreRoleNames)->values(),
        ]);
    }

    /**
     * Update user roles.
     */
    public function updateRoles(ManageUserRolesRequest $request, User $user): RedirectResponse
    {

        $roles = Role::whereIn('id', $request->roles)->get();
        $user->syncRoles($roles);

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'تم تحديث أدوار المستخدم بنجاح.');
    }
}
