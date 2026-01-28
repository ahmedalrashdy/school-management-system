<?php

namespace App\Livewire\Dashboard\Attendance;

use App\Enums\AttendanceModeEnum;
use App\Enums\DayPartEnum;
use App\Enums\RelationToStudentEnum;
use App\Models\SchoolDay;
use App\Services\Attendances\AttendanceSectionService;
use App\Services\Attendances\AttendanceTrackingService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AttendanceDashboard extends Component
{
    // Current attendance mode
    public int $attendanceMode;

    // Selected day part for SplitDaily mode
    public int $selectedDayPart;

    // Selected grade ID for PerPeriod mode (managed by Alpine.js now)
    public ?int $selectedGradeId = null;

    // Selected section ID for PerPeriod mode (managed by Alpine.js now)
    public ?int $selectedSectionId = null;

    // Expanded grade IDs (for accordion state)
    public array $expandedGrades = [];

    public function mount(): void
    {
        // Get attendance mode from settings (mock: using PerPeriod for testing)
        $this->attendanceMode = school()->getAttendanceMode()->value;
        // Default to first part for SplitDaily
        $this->selectedDayPart = DayPartEnum::PART_ONE_ONLY->value;
    }

    /**
     * Switch between day parts (for SplitDaily mode)
     */
    public function switchDayPart(int $part): void
    {
        $this->selectedDayPart = $part;
    }

    public function dayPart()
    {
        if ($this->attendanceMode == AttendanceModeEnum::PerPeriod) {
            return null;
        }
        if ($this->attendanceMode == AttendanceModeEnum::Daily->value) {
            return DayPartEnum::FULL_DAY->value;
        }

        return $this->selectedDayPart;
    }

    #[Computed()]
    public function grades()
    {

        $day = $this->currentSchoolDay;
        return $this->attendanceMode != AttendanceModeEnum::PerPeriod->value ?
            app(AttendanceTrackingService::class)
                ->getDailyStats($day, $this->dayPart())
            : app(AttendanceTrackingService::class)->getPeriodStats($day);

    }

    #[Computed()]
    public function currentSchoolDay(): ?SchoolDay
    {
        return SchoolDay::query()->first();
        return SchoolDay::find(407);
    }

    /**
     * Get today's date info
     */
    public function getTodayInfoProperty(): array
    {
        $today = now();

        return [
            'date' => $today->translatedFormat('l j F Y'),
            'is_school_day' => true,
        ];
    }

    public function getAttendanceModeEnumProperty(): AttendanceModeEnum
    {
        return AttendanceModeEnum::from($this->attendanceMode);
    }

    #[Computed]
    public function summaryStats()
    {
        $grades = $this->grades;
        $allSections = $grades->pluck('sections')->flatten(1);

        $totalUniqueStudents = $allSections->sum('total_students');
        $totalCapacity = 0;

        if ($this->attendanceMode === AttendanceModeEnum::PerPeriod->value) {
            $allPeriods = $allSections->pluck('periods')->flatten(1);
            $totalPeriodsCount = $allPeriods->count();
            $recordedPeriodsCount = $allPeriods->where('is_recorded', true)->count();
            $recordedPeriods = $allPeriods->where('is_recorded', true);
            $totalCapacity = $recordedPeriods->sum('total_students');
            if ($totalCapacity === 0) {
                $totalCapacity = 1;
            }

            $present = $allPeriods->sum('present');
            $absent = $allPeriods->sum('absent');
            $late = $allPeriods->sum('late');
            $excused = $allPeriods->sum('excused');

        } else {
            return [
                'total_students' => $grades->sum('total_students'),
                'total_capacity' => $grades->sum('total_students'),
                'present' => $grades->sum(callback: 'present'),
                'absent' => $grades->sum(callback: 'absent'),
                'late' => $grades->sum(callback: 'late'),
                'excused' => $grades->sum(callback: 'excused'),

                'total_sections' => $grades->sum(callback: 'total_sections'),
                'recorded_sections' => $grades->sum(callback: 'recorded_sections'),

                'recording_percentage' => $grades->sum(callback: 'recorded_sections') > 0
                    ? round(($grades->sum(callback: 'recorded_sections') / $grades->sum(callback: 'total_sections')) * 100)
                    : 0,
                'mode' => $this->attendanceMode,
            ];
        }

        return [
            'total_students' => $totalUniqueStudents,
            'total_capacity' => $totalCapacity,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'total_sections' => $allSections->count(),
            'recorded_sections' => $allSections->filter(fn($s) => ($s['recorded_periods'] ?? ($s['is_recorded'] ? 1 : 0)) > 0)->count(),
            'recording_percentage' => $totalPeriodsCount > 0
                ? round(($recordedPeriodsCount / $totalPeriodsCount) * 100)
                : 0,
            'mode' => $this->attendanceMode,
        ];
    }


    public function loadSectionStudents(int $sectionId): array
    {


        $schoolDay = $this->currentSchoolDay;
        // 1. تحديد رقم ورقة التحضير بناءً على النظام اليومي/الجزئي
        $sheetId = DB::table('attendance_sheets')
            ->where('school_day_id', $schoolDay->id)
            ->where('section_id', $sectionId)
            ->when($this->dayPart(), fn($q) => $q->where('day_part', $this->dayPart()))
            ->when(
                $this->attendanceMode != AttendanceModeEnum::PerPeriod,
                fn($q) => $q->whereNull('timetable_slot_id')
            )
            ->value('id');

        // 2. استدعاء الدالة الموحدة لجلب البيانات
        return $this->fetchAbsentStudentsWithGuardians($sectionId, $sheetId);
    }
    public function loadPeriodStudents(int $sectionId, int $slotId): array
    {
        $schoolDay = $this->currentSchoolDay;

        // 1. تحديد رقم ورقة التحضير بناءً على الحصة
        $sheetId = DB::table('attendance_sheets')
            ->where('school_day_id', $schoolDay->id)
            ->where('section_id', $sectionId)
            ->where('timetable_slot_id', $slotId)
            ->value('id');

        return $this->fetchAbsentStudentsWithGuardians($sectionId, $sheetId);
    }

    /**
     * جلب الطلاب الغائبين مع بيانات أولياء أمورهم
     * يستخدم Eloquent relationships لتجنب التكرار وتحسين الأداء
     */
    private function fetchAbsentStudentsWithGuardians(int $sectionId, ?int $sheetId): array
    {
        if (!$sheetId) {
            return [];
        }
        // جلب الطلاب الغائبين أو غير المسجلين في الشعبة المحددة
        $students = app(AttendanceSectionService::class)->getAbsentStudentsList(
            $sectionId,
            [$sheetId],
            ['students.id', 'students.admission_number', 'students.user_id']
        );
        $students->load([
            'guardians.user:id,first_name,last_name,phone_number',
            'attendances' => fn($q) => $q->where('attendance_sheet_id', $sheetId)
        ]);
        return $students->map(function ($student) {
            $attendance = $student->attendances->first();
            $guardian = $student->guardians->first();

            // تحديد صلة القرابة
            $relationLabel = 'غير محدد';
            if ($guardian && $guardian->pivot->relation_to_student) {
                try {
                    $relationLabel = RelationToStudentEnum::from($guardian->pivot->relation_to_student)->label();
                } catch (\ValueError $e) {
                    // في حالة وجود قيمة غير صحيحة، نستخدم القيمة الافتراضية
                }
            }

            return [
                'id' => $student->id,
                'admission_number' => $student->admission_number,
                'name' => $student->user->first_name . ' ' . $student->user->last_name,
                'status' => $attendance?->status,
                // بيانات ولي الأمر
                'guardian_name' => $guardian ? ($guardian->user->first_name . ' ' . $guardian->user->last_name) : null,
                'guardian_phone' => $guardian?->user->phone_number,
                'relation_to_student' => $guardian?->pivot->relation_to_student,
                'relation_label' => $relationLabel,
            ];
        })->toArray();
    }



    public function render()
    {
        return view('livewire.dashboard.attendance.attendance-dashboard')
            ->layout('components.layouts.dashboard', ['pageTitle' => 'لوحة تحكم الحضور والغياب']);
    }
}
