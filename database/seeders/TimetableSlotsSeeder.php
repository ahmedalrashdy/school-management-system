<?php

namespace Database\Seeders;

use App\Enums\DayOfWeekEnum;
use App\Models\TeacherAssignment;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use DB;
use Illuminate\Database\Seeder;

class TimetableSlotsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // جلب جميع الجداول الدراسية
            $timetables = Timetable::with(['section', 'timetableSetting'])->get();

            if ($timetables->isEmpty()) {
                $this->command->error('لا توجد جداول دراسية. يرجى تشغيل TimetablesSeeder أولاً.');

                return;
            }

            $this->command->info("بدء بناء الحصص الدراسية لـ {$timetables->count()} جدول دراسي...");

            $slotsCreated = 0;
            $timetablesProcessed = 0;

            foreach ($timetables as $timetable) {
                // جلب إعدادات الجدول
                $setting = $timetable->timetableSetting;
                if (! $setting) {
                    $this->command->warn("لا توجد إعدادات للجدول {$timetable->id}");

                    continue;
                }

                $periodsPerDay = $setting->periods_per_day ?? [];
                $durationMinutes = $setting->default_period_duration_minutes ?? 45;

                // جلب تعيينات المدرسين للشعبة
                $teacherAssignments = TeacherAssignment::where('section_id', $timetable->section_id)
                    ->with('teacher')
                    ->get();

                if ($teacherAssignments->isEmpty()) {
                    $this->command->warn("لا توجد تعيينات مدرسين للشعبة {$timetable->section_id}");

                    continue;
                }

                // إنشاء الحصص الدراسية بشكل ثابت (بدون عشوائية)
                $slotsForTimetable = $this->buildTimetableSlots(
                    $timetable,
                    $teacherAssignments,
                    $periodsPerDay,
                    $durationMinutes
                );

                $slotsCreated += count($slotsForTimetable);
                $timetablesProcessed++;

                if ($timetablesProcessed % 50 == 0) {
                    $this->command->info("تم معالجة {$timetablesProcessed} جدول دراسي...");
                }
            }

            $this->command->info("تم إنشاء {$slotsCreated} حصة دراسية في {$timetablesProcessed} جدول دراسي.");
        });
    }

    /**
     * بناء الحصص الدراسية للجدول.
     */
    protected function buildTimetableSlots(
        Timetable $timetable,
        $teacherAssignments,
        array $periodsPerDay,
        int $durationMinutes
    ): array {
        $slots = [];
        // إنشاء قائمة مرتبة بجميع الحصص المتاحة
        $availableSlots = [];
        foreach (DayOfWeekEnum::cases() as $day) {
            $dayKey = $day->key();
            $periodsCount = $periodsPerDay[$dayKey] ?? 0;
            for ($period = 1; $period <= $periodsCount; $period++) {
                $availableSlots[] = [
                    'day' => $day,
                    'period' => $period,
                ];
            }
        }

        $assignments = $teacherAssignments->values();
        $assignmentsCount = $assignments->count();
        if ($assignmentsCount === 0) {
            return $slots;
        }

        // توزيع تعيينات المدرسين بشكل دائري ثابت
        $assignmentIndex = 0;
        foreach ($availableSlots as $slotData) {
            $day = $slotData['day'];
            $period = $slotData['period'];

            $existingSlot = TimetableSlot::where('timetable_id', $timetable->id)
                ->where('day_of_week', $day->value)
                ->where('period_number', $period)
                ->first();

            if ($existingSlot) {
                continue;
            }

            $assignment = $assignments[$assignmentIndex % $assignmentsCount];
            $assignmentIndex++;

            TimetableSlot::create([
                'timetable_id' => $timetable->id,
                'teacher_assignment_id' => $assignment->id,
                'day_of_week' => $day->value,
                'period_number' => $period,
                'duration_minutes' => $durationMinutes,
            ]);

            $slots[] = [
                'day' => $day->value,
                'period' => $period,
                'assignment' => $assignment->id,
            ];
        }

        return $slots;
    }
}
