<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
