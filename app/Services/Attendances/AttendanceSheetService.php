<?php

namespace App\Services\Attendances;

use App\Enums\AttendanceModeEnum;
use App\Enums\DayPartEnum;
use App\Models\AttendanceSheet;
use App\Models\SchoolDay;
use App\Models\Section;
use App\Models\TimetableSlot;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class AttendanceSheetService
{
    /**
     * Get or create an attendance sheet based on the attendance mode.
     */
    public function getOrCreateSheet(
        Section $section,
        SchoolDay $schoolDay,
        ?TimetableSlot $timetableSlot = null,
        ?DayPartEnum $dayPart = null
    ): AttendanceSheet {
        $attendanceMode = $this->getAttendanceMode();

        // Validate mode and parameters
        $this->validateModeParameters($attendanceMode, $timetableSlot, $dayPart);

        // Build unique key based on mode
        $uniqueKey = $this->buildUniqueKey($attendanceMode, $section, $schoolDay, $timetableSlot, $dayPart);

        // Find or create attendance sheet
        $sheet = AttendanceSheet::firstOrCreate(
            $uniqueKey,
            [
                'taken_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]
        );

        return $sheet;
    }



    /**
     * Check if attendance can be recorded for the given parameters.
     */
    public function canRecordAttendance(
        Section $section,
        SchoolDay $schoolDay,
        ?TimetableSlot $timetableSlot = null,
        ?DayPartEnum $dayPart = null
    ): bool {
        // Check if it's a school day
        if ($schoolDay->status->value !== \App\Enums\SchoolDayType::SchoolDay->value) {
            return false;
        }

        // Check if it's a partial holiday and day part matches
        if ($schoolDay->status->value === \App\Enums\SchoolDayType::PartialHoliday->value) {
            if ($dayPart) {
                // For SplitDaily mode, check if the day part matches the school day part
                $schoolDayPart = $schoolDay->day_part;
                if ($schoolDayPart->value === DayPartEnum::PART_ONE_ONLY->value && $dayPart->value !== DayPartEnum::PART_ONE_ONLY->value) {
                    return false;
                }
                if ($schoolDayPart->value === DayPartEnum::PART_TWO_ONLY->value && $dayPart->value !== DayPartEnum::PART_TWO_ONLY->value) {
                    return false;
                }
            }
        }

        // Check if sheet exists and is locked
        $sheet = $this->findSheet($section, $schoolDay, $timetableSlot, $dayPart);
        if ($sheet && $sheet->isLocked()) {
            return false;
        }

        return true;
    }

    /**
     * Get the current attendance mode from school settings.
     */
    public function getAttendanceMode(): AttendanceModeEnum
    {
        $mode = school()->schoolSetting('attendence_mode', AttendanceModeEnum::PerPeriod->value);

        return AttendanceModeEnum::from($mode);
    }

    /**
     * Validate mode parameters.
     */
    private function validateModeParameters(
        AttendanceModeEnum $mode,
        ?TimetableSlot $timetableSlot,
        ?DayPartEnum $dayPart
    ): void {
        switch ($mode) {
            case AttendanceModeEnum::PerPeriod:
                if (!$timetableSlot) {
                    throw new \InvalidArgumentException('TimetableSlot is required for PerPeriod mode');
                }
                break;
            case AttendanceModeEnum::Daily:
                if ($dayPart && $dayPart->value !== DayPartEnum::FULL_DAY->value) {
                    throw new \InvalidArgumentException('DayPart must be FULL_DAY for Daily mode');
                }
                break;
            case AttendanceModeEnum::SplitDaily:
                if (!$dayPart || $dayPart->value === DayPartEnum::FULL_DAY->value) {
                    throw new \InvalidArgumentException('DayPart must be PART_ONE_ONLY or PART_TWO_ONLY for SplitDaily mode');
                }
                break;
        }
    }

    /**
     * Build unique key for attendance sheet based on mode.
     */
    private function buildUniqueKey(
        AttendanceModeEnum $mode,
        Section $section,
        SchoolDay $schoolDay,
        ?TimetableSlot $timetableSlot,
        ?DayPartEnum $dayPart
    ): array {
        $key = [
            'school_day_id' => $schoolDay->id,
            'section_id' => $section->id,
        ];

        switch ($mode) {
            case AttendanceModeEnum::PerPeriod:
                $key['timetable_slot_id'] = $timetableSlot->id;
                $key['day_part'] = null;
                break;
            case AttendanceModeEnum::Daily:
                $key['timetable_slot_id'] = null;
                $key['day_part'] = DayPartEnum::FULL_DAY->value;
                break;
            case AttendanceModeEnum::SplitDaily:
                $key['timetable_slot_id'] = null;
                $key['day_part'] = $dayPart->value;
                break;
        }

        return $key;
    }

    /**
     * Find existing attendance sheet.
     */
    private function findSheet(
        Section $section,
        SchoolDay $schoolDay,
        ?TimetableSlot $timetableSlot = null,
        ?DayPartEnum $dayPart = null
    ): ?AttendanceSheet {
        $attendanceMode = $this->getAttendanceMode();
        $uniqueKey = $this->buildUniqueKey($attendanceMode, $section, $schoolDay, $timetableSlot, $dayPart);
        return AttendanceSheet::where($uniqueKey)->first();
    }


}
