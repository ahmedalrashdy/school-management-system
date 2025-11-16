<?php

namespace App\Services\Attendances;

use App\Enums\AttendanceStatusEnum;
use App\Enums\DayOfWeekEnum;
use App\Enums\DayPartEnum;
use App\Enums\SchoolDayType;
use App\Models\SchoolDay;
use Illuminate\Support\Facades\DB;

class AttendanceTrackingService
{


    public function getDailyStats(SchoolDay $schoolDay, ?int $dayPart = null)
    {
        if ($schoolDay->status == SchoolDayType::PartialHoliday && in_array($dayPart, [DayPartEnum::PART_ONE_ONLY->value, DayPartEnum::PART_TWO_ONLY->value])) {
            throw new \InvalidArgumentException("يجب ان تكون الفترة صحيحة");
        }

        $sections = DB::table('sections')
            ->where('sections.academic_year_id', $schoolDay->academic_year_id)
            ->where('sections.academic_term_id', $schoolDay->academic_term_id)
            ->join('grades', 'sections.grade_id', '=', 'grades.id')
            ->select([
                'grades.id as grade_id',
                'grades.name as grade_name',
                'sections.id as section_id',
                'sections.name as section_name',
                'total_students' => DB::table('section_students')
                    ->whereColumn('section_id', 'sections.id')
                    ->selectRaw('count(*)'),
                'attendance_sheets.id as sheet_id',
                'attendance_sheets.locked_at',
            ])
            ->leftJoin('attendance_sheets', function ($join) use ($schoolDay, $dayPart) {
                $join->on('sections.id', '=', 'attendance_sheets.section_id')
                    ->where('attendance_sheets.school_day_id', '=', $schoolDay->id)
                    ->where('attendance_sheets.timetable_slot_id', '=', null); // تأكيد أنه سجل يومي وليس حصة
                $join->where('attendance_sheets.day_part', '=', $dayPart);
            })
            ->orderBy('grades.sort_order')
            ->orderBy('sections.name')
            ->get();


        $sheetIds = $sections->pluck('sheet_id')->filter()->unique()->toArray();
        $stats = $this->getAttendanceAggregates($sheetIds);

        return $sections->groupBy('grade_id')->map(function ($gradeSections) use ($stats, $sheetIds) {

            $processedSections = $gradeSections->map(function ($sec) use ($stats, $sheetIds) {
                // 2. حساب الأرقام باستخدام الدالة المساعدة
                $sheetStats = $sec->sheet_id !== null ? $stats->get($sec->sheet_id) : null;
                $resolved = $this->resolveStats(
                    $sheetStats,
                    $sec->total_students,
                    in_array($sec->sheet_id, $sheetIds)
                );

                return [
                    'id' => $sec->section_id,
                    'name' => $sec->section_name,
                    'total_students' => $sec->total_students,
                    'is_recorded' => $sec->sheet_id !== null,
                    'is_locked' => $sec->locked_at !== null,

                    'present' => $resolved['present'],
                    'absent' => $resolved['absent'],
                    'late' => $resolved['late'],
                    'excused' => $resolved['excused'],
                ];
            });

            $first = $gradeSections->first();

            return [
                'id' => $first->grade_id,
                'name' => $first->grade_name,
                'sections' => $processedSections,
                'total_sections' => $processedSections->count(),
                'recorded_sections' => $processedSections->where('is_recorded', true)->count(),
                'total_students' => $processedSections->sum('total_students'),
                'present' => $processedSections->sum('present'),
                'absent' => $processedSections->sum('absent'),
                'late' => $processedSections->sum('late'),
                'excused' => $processedSections->sum('excused'),
            ];
        })->values();
    }

    public function getPeriodStats(SchoolDay $schoolDay)
    {
        if ($schoolDay->status === SchoolDayType::Holiday) {
            return [];
        }

        $sectionsData = DB::table('sections')
            ->where('sections.academic_year_id', $schoolDay->academic_year_id)
            ->where('sections.academic_term_id', $schoolDay->academic_term_id)
            ->join('grades', 'sections.grade_id', '=', 'grades.id')
            ->join('timetables', 'sections.id', '=', 'timetables.section_id')
            ->join('timetable_slots', 'timetables.id', '=', 'timetable_slots.timetable_id')
            // Metadata joins
            ->join('teacher_assignments', 'timetable_slots.teacher_assignment_id', '=', 'teacher_assignments.id')
            ->join('curriculum_subject', 'teacher_assignments.curriculum_subject_id', '=', 'curriculum_subject.id')
            ->join('subjects', 'curriculum_subject.subject_id', '=', 'subjects.id')
            ->join('teachers', 'teacher_assignments.teacher_id', '=', 'teachers.id')
            ->join('users', 'teachers.user_id', '=', 'users.id')
            // Partial holiday logic
            ->when($schoolDay->status === SchoolDayType::PartialHoliday, function ($query) use ($schoolDay) {
                $query->join('timetable_settings', 'timetables.timetable_setting_id', 'timetable_settings.id');
                if ($schoolDay->day_part === DayPartEnum::PART_ONE_ONLY) {
                    $query->whereColumn('timetable_slots.period_number', '<=', 'timetable_settings.periods_before_break');
                } else {
                    $query->whereColumn('timetable_slots.period_number', '>', 'timetable_settings.periods_before_break');
                }
            })
            ->where('timetables.is_active', true)
            ->where('timetable_slots.day_of_week', DayOfWeekEnum::fromCarbonDayOfWeek($schoolDay->date->dayOfWeek))
            ->leftJoin('attendance_sheets', function ($join) use ($schoolDay) {
                $join->on('timetable_slots.id', '=', 'attendance_sheets.timetable_slot_id')
                    ->where('attendance_sheets.school_day_id', '=', $schoolDay->id);
            })
            ->select([
                'grades.id as grade_id',
                'grades.name as grade_name',
                'sections.id as section_id',
                'sections.name as section_name',
                'timetable_slots.id as slot_id',
                'timetable_slots.period_number',
                'subjects.name as subject_name',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as teacher_name"),
                'total_students' => DB::table('section_students')
                    ->whereColumn('section_id', 'sections.id')
                    ->selectRaw('count(*)'),
                'attendance_sheets.id as sheet_id',
                'attendance_sheets.locked_at',
            ])
            ->orderBy('grades.sort_order')
            ->orderBy('sections.name')
            ->orderBy('timetable_slots.period_number')
            ->get();

        // 1. جلب الإحصائيات باستخدام الدالة المساعدة
        $sheetIds = $sectionsData->pluck('sheet_id')->filter()->unique()->toArray();
        $stats = $this->getAttendanceAggregates($sheetIds);

        return $sectionsData->groupBy('grade_id')->map(function ($gradeRows) use ($stats, $sheetIds) {
            $firstGradeRow = $gradeRows->first();

            $sections = $gradeRows->groupBy('section_id')->map(function ($slotRows) use ($stats, $sheetIds) {
                $firstSectionRow = $slotRows->first();

                $periods = $slotRows->map(function ($row) use ($stats, $sheetIds) {
                    // 2. حساب الأرقام باستخدام الدالة المساعدة
                    $sheetStats = $row->sheet_id !== null ? $stats->get($row->sheet_id) : null;
                    $resolved = $this->resolveStats(
                        $sheetStats,
                        $row->total_students,
                        in_array($row->sheet_id, $sheetIds)
                    );

                    return [
                        'slot_id' => $row->slot_id,
                        'number' => $row->period_number,
                        'subject' => $row->subject_name,
                        'teacher_name' => $row->teacher_name,
                        'is_recorded' => $row->sheet_id !== null,
                        'is_locked' => $row->locked_at !== null,
                        'total_students' => $row->total_students,

                        'present' => $resolved['present'],
                        'absent' => $resolved['absent'],
                        'late' => $resolved['late'],
                        'excused' => $resolved['excused'],
                    ];
                });

                return [
                    'id' => $firstSectionRow->section_id,
                    'name' => $firstSectionRow->section_name,
                    'total_students' => $firstSectionRow->total_students,
                    'periods' => $periods->values(),
                    'total_periods' => $periods->count(),
                    'recorded_periods' => $periods->where('is_recorded', true)->count(),
                ];
            })->values();

            return [
                'id' => $firstGradeRow->grade_id,
                'name' => $firstGradeRow->grade_name,
                'sections' => $sections,
                'total_students' => $sections->sum('total_students'),
                'total_sections' => $sections->count(),
                'recorded_sections' => $sections->where('recorded_periods', '>', 0)->count(),
            ];
        })->values();
    }


    /**
     * دالة مساعدة لجلب إحصائيات الحضور الخام لمجموعة من الأوراق
     */
    private function getAttendanceAggregates(array $sheetIds): \Illuminate\Support\Collection
    {
        if (empty($sheetIds)) {
            return collect();
        }

        return DB::table('attendances')
            ->whereIn('attendance_sheet_id', $sheetIds)
            ->select('attendance_sheet_id')
            ->selectRaw("count(case when status = ? then 1 end) as present", [AttendanceStatusEnum::Present->value])
            ->selectRaw("count(case when status = ? then 1 end) as explicit_absent", [AttendanceStatusEnum::Absent->value])
            ->selectRaw("count(case when status = ? then 1 end) as late", [AttendanceStatusEnum::Late->value])
            ->selectRaw("count(case when status = ? then 1 end) as excused", [AttendanceStatusEnum::Excused->value])
            ->selectRaw("count(*) as total_records")
            ->groupBy('attendance_sheet_id')
            ->get()
            ->keyBy('attendance_sheet_id');
    }

    /**
     * دالة مساعدة لحساب الأرقام النهائية وتطبيق منطق الغياب الضمني
     */
    private function resolveStats(?object $sheetStats, int $totalStudents, bool $isSheetExists): array
    {
        if (!$isSheetExists) {
            return [
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
            ];
        }
        if (!$sheetStats) {
            return [
                'present' => 0,
                'absent' => $totalStudents,//all student absent
                'late' => 0,
                'excused' => 0,
            ];
        }

        $missingRecords = max(0, $totalStudents - $sheetStats->total_records);
        $realAbsent = $sheetStats->explicit_absent + $missingRecords;

        return [
            'present' => (int) $sheetStats->present,
            'absent' => (int) $realAbsent,
            'late' => (int) $sheetStats->late,
            'excused' => (int) $sheetStats->excused,
        ];
    }

}
