<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing permissions cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions from the enum
        foreach (PermissionEnum::cases() as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission->value],
                ['guard_name' => 'web']
            );
        }

        $permenentRoles = [
            ['name' => 'مدرس', 'guard_name' => 'web'],
            ['name' => 'طالب', 'guard_name' => 'web'],
            ['name' => 'ولي أمر', 'guard_name' => 'web'],
            ['name' => 'موظف', 'guard_name' => 'web'],
        ];
        Role::upsert($permenentRoles, ['name', 'guard_name']);
    }
}
