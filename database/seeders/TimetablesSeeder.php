<?php

namespace Database\Seeders;

use App\Enums\AcademicYearStatus;
use App\Models\Section;
use App\Models\Timetable;
use App\Models\TimetableSetting;
use DB;
use Illuminate\Database\Seeder;

class TimetablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. إنشاء قالب واحد لإعدادات الجداول الدراسية
            $timetableSetting = TimetableSetting::firstOrCreate([
                'name' => 'القالب الافتراضي',
            ], [
                'is_active' => true,
                'periods_per_day' => [
                    'sunday' => 7,
                    'monday' => 7,
                    'tuesday' => 7,
                    'wednesday' => 7,
                    'thursday' => 6,
                    'friday' => 0, // يوم الجمعة عطلة
                    'saturday' => 0, // يوم السبت عطلة
                ],
                'first_period_start_time' => '08:00:00',
                'default_period_duration_minutes' => 45,
                'periods_before_break' => 3,
                'break_duration_minutes' => 30,
            ]);

            $this->command->info('تم إنشاء قالب إعدادات الجداول الدراسية.');

            // 2. جلب جميع الشعب الدراسية
            $sections = Section::with(['academicYear', 'grade', 'academicTerm'])->get();

            if ($sections->isEmpty()) {
                $this->command->error('لا توجد شعب دراسية. يرجى تشغيل SectionsAndCurriculumsSeeder أولاً.');

                return;
            }

            $this->command->info("بدء إنشاء الجداول الدراسية لـ {$sections->count()} شعبة...");

            $timetablesCreated = 0;

            // 3. إنشاء جدول دراسي لكل شعبة
            foreach ($sections as $section) {
                // إنشاء اسم للجدول الدراسي
                $timetableName = "جدول {$section->grade->name} - شعبة {$section->name} - {$section->academicYear->name} - {$section->academicTerm->name}";
                $isActive = $section->academicYear?->status === AcademicYearStatus::Active
                    && (bool) $section->academicTerm?->is_active;

                // إنشاء الجدول الدراسي (غير نشط افتراضياً)
                Timetable::firstOrCreate([
                    'section_id' => $section->id,
                    'is_active' => $isActive,
                ], [
                    'name' => $timetableName,
                    'timetable_setting_id' => $timetableSetting->id,
                ]);

                $timetablesCreated++;

                if ($timetablesCreated % 50 == 0) {
                    $this->command->info("تم إنشاء {$timetablesCreated} جدول دراسي...");
                }
            }

            $this->command->info("تم إنشاء {$timetablesCreated} جدول دراسي.");
        });
    }
}
