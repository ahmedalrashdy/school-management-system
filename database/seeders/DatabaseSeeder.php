<?php

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Cache::clear();
        User::updateOrCreate(
            ['email' => 'guest@school.test'],
            [
                'first_name' => 'Guest',
                'last_name' => 'Admin',
                'gender' => GenderEnum::Male->value,
                'password' => '12345678',
                'is_active' => true,
                'is_admin' => true,
                'is_guest' => true,
                'reset_password_required' => false,
            ]
        );

        // User::factory(10)->create();

        // User::factory()->admin()->create([
        //     'first_name' => 'Super',
        //     'last_name' => 'Admin',
        // ]);

        $this->call([
            PermissionSeeder::class,
            SchoolSettingSeeder::class,
            SchoolBasicSeeder::class,
            SectionsAndCurriculumsSeeder::class,
            SchoolDaysSeeder::class,
            StudentsSeeder::class,
            GuardiansSeeder::class,
            TeachersSeeder::class,
            TimetablesSeeder::class,
            TimetableSlotsSeeder::class,
            ExamsAndGradingRulesSeeder::class,
            AttendancesSeeder::class,
            MarksSeeder::class,
        ]);
    }
}
