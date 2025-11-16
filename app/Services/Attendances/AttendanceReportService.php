<?php

namespace App\Services\Attendances;

use App\Enums\AttendanceStatusEnum;
use App\Enums\SchoolDayType;
use App\Models\SchoolDay;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
class AttendanceReportService
{

    public function getAttendancesStats(
        int $academicTermId,
        Carbon $startDate,
        Carbon $endDate,
        bool $chunkFetchingAttendances = true,
    ): Collection {
        //schoolDays days withouts holidays
        $schoolDays = SchoolDay::query()
            ->where('academic_term_id', $academicTermId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotIn('status', [SchoolDayType::Holiday])
            ->with(['attendanceSheets:id,school_day_id,section_id'])
            ->get(['id', 'date', 'status', 'day_part']);
        //remove school days have not aatendanceSheets
        $schoolDays = $schoolDays->filter(fn($day) => $day->attendanceSheets->count() > 0);
        $summaryDetails = collect([
            "total_days" => $schoolDays->count(),
            'details' => collect([]),
        ]);
        if ($schoolDays->isEmpty()) {
            return $summaryDetails;
        }

        $sectionsIds = $schoolDays->pluck("attendanceSheets.*.section_id")->flatten()->unique();
        //طلاب الشعب التي يفترض أن  يتم تحضيرهم ( اي طالب لم يتم رصده يتم إعتباره غائب )
        $sectionsStudents = DB::table('section_students')
            ->whereIn("section_id", $sectionsIds)
            ->select('section_id', 'student_id')
            ->get()
            ->groupBy('section_id');



        if (!$chunkFetchingAttendances) {
            $allSheetsIds = $schoolDays->pluck('attendanceSheets.*.id')->flatten();
            $attendances = $this->attendancesOfSheets($allSheetsIds)
                ->groupBy('student_id')
                ->map(fn($items) => $items->keyBy('attendance_sheet_id'));
        }
        //schoolDays->sectionsSheets->sectionStudents
        foreach ($schoolDays as $schoolDay) {
            $daySheets = $schoolDay->attendanceSheets->groupBy("section_id");
            if ($chunkFetchingAttendances) {
                //سجلات الحضور chunk
                $attendances = $this->attendancesOfSheets($schoolDay->attendanceSheets->pluck("id"))
                    ->groupBy('student_id')
                    ->map(fn($items) => $items->keyBy('attendance_sheet_id'));
            }


            foreach ($daySheets as $sectionId => $sectionSheets) {
                if (isset($sectionsStudents[$sectionId])) {//يكون غير موجود عندما لا يكون في الشعبه  طلاب
                    $sectionStats = $this->sectionAttendanceStats(
                        $attendances,
                        $sectionSheets,
                        $sectionsStudents[$sectionId]
                    );
                    $summaryDetails['details']->add([
                        'stats' => $sectionStats,
                        'section_id' => $sectionId,
                        'date' => $schoolDay->date,
                        'school_day_id' => $schoolDay->id,
                        'school_day_part' => $schoolDay->status === SchoolDayType::PartialHoliday
                            ? $schoolDay->day_part : null,
                    ]);
                }

            }
        }
        return $summaryDetails;
    }


    public function attendancesOfSheets(Collection $sheetsIds)
    {
        return DB::table('attendances')
            ->whereIn('attendance_sheet_id', $sheetsIds)
            ->select(['student_id', 'attendance_sheet_id', 'status'])
            ->get();
    }

    public function sectionAttendanceStats(
        Collection $attendances,
        Collection $sectionSheets,
        Collection $sectionStudents
    ) {
        $stats = $this->getEmptyStats();
        $totalSlots = $sectionSheets->count();
        foreach ($sectionStudents as $student) {
            $studentAttendancesRecords = $attendances->get($student->student_id);
            $finalStatus = $this->calculateStudentAttendanceStatusOfDay(
                $studentAttendancesRecords,
                $sectionSheets,
                $totalSlots,
            );
            $stats[$finalStatus] += 1;
        }
        return $stats;
    }

    public function calculateStudentAttendanceStatusOfDay(
        $studentAttendancesRecords,
        $sectionSheets,
        $totalSlots,
    ) {

        $counts = [
            AttendanceStatusEnum::Present->value => 0,
            AttendanceStatusEnum::Absent->value => 0,
            AttendanceStatusEnum::Late->value => 0,
            AttendanceStatusEnum::Excused->value => 0,
        ];
        foreach ($sectionSheets as $targetSheet) {
            $record = $studentAttendancesRecords?->get($targetSheet->id);

            if ($record) {
                $counts[$record->status]++;
            } else {
                $counts[AttendanceStatusEnum::Absent->value]++; // Default Absent
            }
        }
        // تطبيق "نفس أسلوب" الحكم النهائي على اليوم
        $finalStatus = $this->determineDayStatusFromCounts($counts, $totalSlots);
        return $finalStatus;

    }
    public function getEmptyStats(): Collection
    {
        return collect(
            [
                AttendanceStatusEnum::Present->value => 0,
                AttendanceStatusEnum::Absent->value => 0,
                AttendanceStatusEnum::Late->value => 0,
                AttendanceStatusEnum::Excused->value => 0,
                'partial_absence' => 0,
                'present_with_late' => 0,
                'partial_excused' => 0,
            ]
        );
    }

    /**
     * منطق تحديد الحالة النهائية لليوم
     */
    private function determineDayStatusFromCounts(array $counts, int $totalSlots): string
    {
        $presentCount = $counts[AttendanceStatusEnum::Present->value] ?? 0;
        $absentCount = $counts[AttendanceStatusEnum::Absent->value] ?? 0;
        $lateCount = $counts[AttendanceStatusEnum::Late->value] ?? 0;
        $excusedCount = $counts[AttendanceStatusEnum::Excused->value] ?? 0;

        // 1. حالات التطابق الكامل
        if ($presentCount == $totalSlots) {
            return AttendanceStatusEnum::Present->value;
        }
        if ($absentCount == $totalSlots) {
            return AttendanceStatusEnum::Absent->value;
        }
        if ($excusedCount == $totalSlots) {
            return AttendanceStatusEnum::Excused->value;
        }
        if ($lateCount == $totalSlots) {
            return AttendanceStatusEnum::Late->value;
        }

        // 2. حالات مختلطة (Partial)
        if ($absentCount > 0) {

            return 'partial_absence';
        }
        if ($excusedCount > 0) {
            return 'partial_excused';
        }
        if ($lateCount > 0) {
            return 'present_with_late';
        }

        // الحالة الافتراضية إذا تبقت حالات نادرة
        return AttendanceStatusEnum::Present->value;
    }



    public function getAttendanceSummaryStats($stats)
    {
        $initialStats = $this->getEmptyStats();
        return $stats->reduce(function ($carry, $partStats) {
            foreach ($partStats as $key => $value) {
                $carry[$key] += $value;
            }

            return $carry;
        }, $initialStats);
    }
    public function sectionStudentsAttendanceStats(
        Collection $attendances,
        Collection $sectionSheets,
        Collection $sectionStudents
    ) {
        $sectionDetails = collect([
            'stats' => $this->getEmptyStats(),
            'details' => collect(),
        ]);
        $totalSlots = $sectionSheets->count();
        foreach ($sectionStudents as $student) {
            $studentAttendancesRecords = $attendances->get($student->student_id);
            $finalStatus = $this->calculateStudentAttendanceStatusOfDay(
                $studentAttendancesRecords,
                $sectionSheets,
                $totalSlots,
            );
            $sectionDetails['stats'][$finalStatus] += 1;
            $sectionDetails['details']->add([
                'student_id' => $student->student_id,
                'status' => $finalStatus,
            ]);
        }
        return $sectionDetails;
    }

    public function getStudentsAttendancesStats(
        EloquentCollection $schoolDays,
        Collection $sectionsStudents,
        bool $chunkFetchingAttendances = true,
    ): Collection {
        $sectionIds = $sectionsStudents->pluck('section_id')->unique();
        $schoolDays->load([
            'attendanceSheets' => function ($q) use ($sectionIds) {
                $q->whereIn('section_id', $sectionIds)
                    ->select(['id', 'school_day_id', 'section_id']);
            }
        ]);
        $schoolDays = $schoolDays->filter(fn($day) => $day->attendanceSheets->count() > 0);
        $sectionsStudents = $sectionsStudents->groupBy('section_id');
        $summaryDetails = collect([
            'total_days' => $schoolDays->count(),
            'details' => collect([]),
        ]);

        if (!$chunkFetchingAttendances) {
            $allSheetsIds = $schoolDays->pluck('attendanceSheets.*.id')->flatten();
            $attendances = $this->attendancesOfSheets($allSheetsIds)
                ->groupBy('student_id')
                ->map(fn($items) => $items->keyBy('attendance_sheet_id'));
        }
        //schoolDays->sectionsSheets->sectionStudents
        foreach ($schoolDays as $schoolDay) {
            $daySheets = $schoolDay->attendanceSheets->groupBy("section_id");
            if ($chunkFetchingAttendances) {
                //سجلات الحضور chunk
                $attendances = $this->attendancesOfSheets($schoolDay->attendanceSheets->pluck("id"))
                    ->groupBy('student_id')
                    ->map(fn($items) => $items->keyBy('attendance_sheet_id'));
            }
            foreach ($daySheets as $sectionId => $sectionSheets) {
                if (isset($sectionsStudents[$sectionId])) {//يكون غير موجود عندما لا يكون في الشعبه  طلاب

                    $sectionDetailsStats = $this->sectionStudentsAttendanceStats(
                        $attendances,
                        $sectionSheets,
                        $sectionsStudents[$sectionId]
                    );

                    $summaryDetails['details']->add([
                        'stats' => $sectionDetailsStats['stats'],
                        'section_id' => $sectionId,
                        'students' => $sectionDetailsStats['details'],
                        'date' => $schoolDay->date,
                        'school_day_id' => $schoolDay->id,
                        'school_day_part' => $schoolDay->status === SchoolDayType::PartialHoliday
                            ? $schoolDay->day_part : null,
                    ]);
                }

            }
        }
        return $summaryDetails;
    }
}
