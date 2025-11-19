<?php

namespace App\Livewire\Common\TeacherProfile;

use App\Models\Teacher;
use App\Models\TimetableSlot;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TeacherTimetable extends Component
{
    public int $teacher_id;

    public ?int $academicYearId = null;

    public ?int $academicTermId = null;

    public function mount(int $teacher_id): void
    {
        $this->teacher_id = $teacher_id;
        $this->academicYearId = $this->academicYearId ?: school()->activeYear()?->id;
        $this->academicTermId = $this->academicTermId ?: school()->currentAcademicTerm()?->id;
    }

    #[Computed()]
    public function teacher()
    {
        return Teacher::findOrFail($this->teacher_id);
    }

    #[Computed]
    public function calendarDays()
    {
        if (! $this->academicYearId || ! $this->academicTermId) {
            return [];
        }

        $slots = $this->teacher->timetableSlots()
            ->with([
                'timetableSetting',
                'teacherAssignment.section.grade',
                'teacherAssignment.curriculumSubject.subject',
            ])
            ->whereHas('timetable', function ($q) {
                $q->where('timetables.is_active', true);
            })
            ->get();

        // 2. تجميع الحصص حسب الأيام وحساب الأوقات
        $days = [];

        $groupedSlots = $slots->groupBy('day_of_week');

        foreach ($groupedSlots as $dayIndex => $daySlots) {
            $processedSlots = $daySlots->map(function ($slot) {
                return $this->calculateSlotTimes($slot);
            })->sortBy('start_time_carbon');

            $days[$dayIndex] = $processedSlots;
        }

        ksort($days);

        return $days;
    }

    /**
     * دالة مساعدة لحساب وقت الحصة بناءً على إعدادات جدولها الخاص
     */
    private function calculateSlotTimes(TimetableSlot $slot)
    {
        $setting = $slot->timetableSetting;

        // وقت بدء اليوم الدراسي لهذه الشعبة
        $startTime = Carbon::parse($setting->first_period_start_time);

        // حساب الوقت المضاف للحصص السابقة
        $minutesToAdd = ($slot->period_number - 1) * $setting->default_period_duration_minutes;

        // إضافة وقت الفسحة إذا كانت الحصة بعد الفسحة
        if ($slot->period_number > $setting->periods_before_break) {
            $minutesToAdd += $setting->break_duration_minutes;
        }

        $startAt = $startTime->copy()->addMinutes($minutesToAdd);
        $endAt = $startAt->copy()->addMinutes($slot->duration_minutes);

        // إضافة خصائص ديناميكية للكائن (للعرض فقط)
        $slot->formatted_start_time = $startAt->format('H:i');
        $slot->formatted_end_time = $endAt->format('H:i');
        $slot->start_time_carbon = $startAt; // للترتيب

        return $slot;
    }

    public function render()
    {

        return view('livewire.common.teacher-profile.teacher-timetable');
    }
}
