<?php

namespace App\Http\Controllers\Dashboard\Attendance;

use App\Enums\DayPartEnum;
use App\Http\Controllers\Controller;
use App\Models\SchoolDay;
use App\Models\Section;
use App\Models\TimetableSlot;

class RecordAttendanceController extends Controller
{
    /**
     * Per Period Mode - Record attendance for a specific period.
     */
    public function index(Section $section, TimetableSlot $timetableSlot, string $date)
    {
        return view('dashboard.attendance.attendances.record', [
            'section' => $section,
            'timetableSlot' => $timetableSlot,
            'date' => $date,
            'schoolDay' => SchoolDay::where('academic_year_id', $section->academic_year_id)
                ->whereDate('date', $date)
                ->firstOrFail(),
            'dayPart' => null,
        ]);
    }

    /**
     * Daily Mode - Record attendance for the full day.
     */
    public function daily(Section $section, SchoolDay $schoolDay)
    {

        return view('dashboard.attendance.attendances.record', [
            'section' => $section,
            'timetableSlot' => null,
            'date' => null,
            'schoolDay' => $schoolDay,
            'dayPart' => null,
        ]);
    }

    /**
     * Split Daily Mode - Record attendance for a specific part of the day.
     */
    public function splitDaily(Section $section, SchoolDay $schoolDay, int $dayPart)
    {
        return view('dashboard.attendance.attendances.record', [
            'section' => $section,
            'timetableSlot' => null,
            'date' => null,
            'schoolDay' => $schoolDay,
            'dayPart' => DayPartEnum::from($dayPart),
        ]);
    }
}
