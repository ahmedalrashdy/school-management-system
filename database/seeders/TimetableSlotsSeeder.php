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

                // إنشاء الحصص الدراسية
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
        $teacherBusySlots = []; // تتبع الحصص المشغولة لكل مدرس [teacher_id => [day => [periods]]]

        // إنشاء قائمة بجميع الحصص المتاحة
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

        // خلط الحصص المتاحة عشوائياً لضمان توزيع أفضل
        shuffle($availableSlots);

        // توزيع تعيينات المدرسين على الحصص المتاحة
        $assignmentIndex = 0;
        $assignmentsCount = $teacherAssignments->count();

        foreach ($availableSlots as $slotData) {
            $day = $slotData['day'];
            $period = $slotData['period'];

            // التحقق من أن الحصة غير موجودة مسبقاً
            $existingSlot = TimetableSlot::where('timetable_id', $timetable->id)
                ->where('day_of_week', $day->value)
                ->where('period_number', $period)
                ->first();

            if ($existingSlot) {
                continue; // الحصة موجودة مسبقاً
            }

            // البحث عن تعيين مدرس متاح لهذا الوقت
            $assignmentPlaced = false;

            // محاولة إيجاد مدرس متاح من جميع التعيينات
            foreach ($teacherAssignments as $assignment) {
                $teacherId = $assignment->teacher_id;

                // التحقق من أن المدرس غير مشغول في هذا الوقت
                if (! $this->isTeacherBusy($teacherId, $day->value, $period, $teacherBusySlots)) {
                    // إنشاء الحصة
                    TimetableSlot::create([
                        'timetable_id' => $timetable->id,
                        'teacher_assignment_id' => $assignment->id,
                        'day_of_week' => $day->value,
                        'period_number' => $period,
                        'duration_minutes' => $durationMinutes,
                    ]);

                    // تسجيل الحصة كـ مشغولة للمدرس
                    if (! isset($teacherBusySlots[$teacherId])) {
                        $teacherBusySlots[$teacherId] = [];
                    }
                    if (! isset($teacherBusySlots[$teacherId][$day->value])) {
                        $teacherBusySlots[$teacherId][$day->value] = [];
                    }
                    $teacherBusySlots[$teacherId][$day->value][] = $period;

                    $assignmentPlaced = true;
                    $slots[] = [
                        'day' => $day->value,
                        'period' => $period,
                        'assignment' => $assignment->id,
                    ];
                    break; // وجدنا مدرس متاح، نخرج من الحلقة
                }
            }

            if (! $assignmentPlaced) {
                // إذا لم يتم العثور على مدرس متاح، نستخدم أول تعيين متاح
                // (قد يكون المدرس مشغولاً في جداول أخرى، لكن سنملأ الحصة)
                $assignment = $teacherAssignments[0];

                TimetableSlot::create([
                    'timetable_id' => $timetable->id,
                    'teacher_assignment_id' => $assignment->id,
                    'day_of_week' => $day->value,
                    'period_number' => $period,
                    'duration_minutes' => $durationMinutes,
                ]);

                // تسجيل الحصة محلياً (حتى لو كان المدرس مشغولاً في جداول أخرى)
                if (! isset($teacherBusySlots[$assignment->teacher_id])) {
                    $teacherBusySlots[$assignment->teacher_id] = [];
                }
                if (! isset($teacherBusySlots[$assignment->teacher_id][$day->value])) {
                    $teacherBusySlots[$assignment->teacher_id][$day->value] = [];
                }
                $teacherBusySlots[$assignment->teacher_id][$day->value][] = $period;

                $slots[] = [
                    'day' => $day->value,
                    'period' => $period,
                    'assignment' => $assignment->id,
                ];

                $this->command->warn("تم تعيين مدرس مشغول في جداول أخرى للحصة (الجدول: {$timetable->id}, اليوم: {$day->value}, الحصة: {$period})");
            }
        }

        return $slots;
    }

    /**
     * التحقق من أن المدرس مشغول في وقت معين.
     */
    protected function isTeacherBusy(int $teacherId, int $dayOfWeek, int $periodNumber, array $teacherBusySlots): bool
    {
        // التحقق من الحصص المحلية (في نفس الجدول)
        if (isset($teacherBusySlots[$teacherId][$dayOfWeek])) {
            if (in_array($periodNumber, $teacherBusySlots[$teacherId][$dayOfWeek])) {
                return true;
            }
        }

        // التحقق من الحصص في جداول أخرى (نفس المدرس، نفس اليوم، نفس رقم الحصة)
        $busyInOtherTimetables = DB::table('timetable_slots')
            ->join('teacher_assignments', 'timetable_slots.teacher_assignment_id', '=', 'teacher_assignments.id')
            ->where('teacher_assignments.teacher_id', $teacherId)
            ->where('timetable_slots.day_of_week', $dayOfWeek)
            ->where('timetable_slots.period_number', $periodNumber)
            ->exists();

        return $busyInOtherTimetables;
    }
}
