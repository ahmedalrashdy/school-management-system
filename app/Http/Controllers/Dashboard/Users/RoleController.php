<?php

namespace App\Http\Controllers\Dashboard\Users;

use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Users\Roles\StoreRoleRequest;
use App\Http\Requests\Dashboard\Users\Roles\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::RolesView->value, only: ['index']),
            new Middleware('can:' . \Perm::RolesCreate->value, only: ['create', 'store']),
            new Middleware('can:' . \Perm::RolesUpdate->value, only: ['edit', 'update']),
            new Middleware('can:' . \Perm::RolesDelete->value, only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->when(
                request()->search,
                fn($q, $search)
                => $q->whereLike('name', "%$search%")->orderBy('name')

            )->latest()->paginate(20);
        return view('dashboard.users.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.users.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Role::create([
            'name' => $request->safe()->input('name'),
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('dashboard.roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $groupedPermissions = PermissionEnum::grouped();
        $selectedPermissions = $role->permissions->pluck('name')->toArray();

        return view('dashboard.users.roles.edit', [
            'role' => $role,
            'groupedPermissions' => $groupedPermissions,
            'selectedPermissions' => $selectedPermissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        DB::transaction(function () use ($request, $role) {
            // Update role name
            $role->update([
                'name' => $request->safe()->input('name'),
            ]);

            // Sync permissions
            $permissions = $request->safe()->array('permissions');
            $role->syncPermissions($permissions);
        });

        return redirect()
            ->route('dashboard.roles.index')
            ->with('success', 'تم تحديث الدور بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Check if role has users assigned
        if ($role->users()->count() > 0) {
            return redirect()
                ->route('dashboard.roles.index')
                ->with('error', 'لا يمكن حذف الدور لأنه مرتبط بمستخدمين. يرجى إعادة تعيين أدوار المستخدمين أولاً.');
        }

        $role->delete();

        return redirect()
            ->route('dashboard.roles.index')
            ->with('success', 'تم حذف الدور بنجاح.');
    }
}
