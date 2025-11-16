<?php

namespace App\Http\Controllers\Dashboard\Attendance;

use App\Enums\AttendanceModeEnum;
use App\Enums\DayPartEnum;
use App\Http\Controllers\Controller;
use App\Models\SchoolDay;
use App\Models\Section;
use App\Models\TimetableSlot;
use App\Services\Attendances\AttendanceSectionService;
use App\Services\Attendances\AttendanceSheetService;

class StudentAttendanceListController extends Controller
{
    public function __construct(
        private AttendanceSectionService $attendanceSectionService,
        private AttendanceSheetService $attendanceSheetService
    ) {
    }

    /**
     * Per Period Mode - Show student list for a specific period.
     */
    public function perPeriod(Section $section, TimetableSlot $timetableSlot, string $date)
    {
        $schoolDay = SchoolDay::where('academic_year_id', $section->academic_year_id)
            ->whereDate('date', $date)
            ->firstOrFail();

        $sheet = $this->attendanceSheetService->getOrCreateSheet($section, $schoolDay, $timetableSlot);

        $students = $this->attendanceSectionService->getAllStudentsWithAttendance($section->id, $sheet->id);

        return view('dashboard.attendance.student-list', [
            'section' => $section,
            'schoolDay' => $schoolDay,
            'timetableSlot' => $timetableSlot,
            'students' => $students,
            'attendanceMode' => AttendanceModeEnum::PerPeriod,
            'modeLabel' => 'حصة دراسية',
            'contextLabel' => "الحصة {$timetableSlot->period_number} - {$timetableSlot->teacherAssignment->curriculumSubject->subject->name}",
        ]);
    }

    /**
     * Daily Mode - Show student list for the full day.
     */
    public function daily(Section $section, SchoolDay $schoolDay)
    {
        $sheet = $this->attendanceSheetService->getOrCreateSheet($section, $schoolDay);

        $students = $this->attendanceSectionService->getAllStudentsWithAttendance($section->id, $sheet->id);

        return view('dashboard.attendance.student-list', [
            'section' => $section,
            'schoolDay' => $schoolDay,
            'timetableSlot' => null,
            'students' => $students,
            'attendanceMode' => AttendanceModeEnum::Daily,
            'modeLabel' => 'يومي كامل',
            'contextLabel' => 'اليوم الدراسي كاملاً',
        ]);
    }

    /**
     * Split Daily Mode - Show student list for a specific part of the day.
     */
    public function splitDaily(Section $section, SchoolDay $schoolDay, int $dayPart)
    {
        $dayPartEnum = DayPartEnum::from($dayPart);
        $sheet = $this->attendanceSheetService->getOrCreateSheet($section, $schoolDay, null, $dayPartEnum);

        $students = $this->attendanceSectionService->getAllStudentsWithAttendance($section->id, $sheet->id);

        return view('dashboard.attendance.student-list', [
            'section' => $section,
            'schoolDay' => $schoolDay,
            'timetableSlot' => null,
            'students' => $students,
            'attendanceMode' => AttendanceModeEnum::SplitDaily,
            'modeLabel' => 'فترة من اليوم',
            'contextLabel' => $dayPartEnum->label(),
            'dayPart' => $dayPartEnum,
        ]);
    }
}
