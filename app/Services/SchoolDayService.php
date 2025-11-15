<?php

namespace App\Services;

use App\Enums\DayOfWeekEnum;
use App\Enums\DayPartEnum;
use App\Enums\SchoolDayType;
use App\Models\AcademicTerm;
use App\Models\SchoolDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SchoolDayService
{
    /**
     * Generate school days for a given academic term.
     */
    public function generateDaysForTerm(AcademicTerm $term): void
    {
         // Get weekend days from settings
         $weekendDays = school()->getArrayData('weekend_days', []);
         if (! is_array($weekendDays)) {
             $weekendDays = [];
         }

         // Prepare bulk insert data
         $daysToInsert = [];
         $currentDate = Carbon::parse($term->start_date)->copy();
         $endDate = Carbon::parse($term->end_date)->copy();

         while ($currentDate->lte($endDate)) {
             // Check if day already exists
             $existingDay = SchoolDay::where('academic_year_id', $term->academic_year_id)
                 ->where('date', $currentDate->toDateString())
                 ->first();

             if ($existingDay) {
                 // Update existing day with academic_term_id if not set
                 if (! $existingDay->academic_term_id) {
                     $existingDay->update(['academic_term_id' => $term->id]);
                 }
                 $currentDate->addDay();

                 continue;
             }

             // Check if it's a weekend day
             $dayOfWeek = DayOfWeekEnum::fromCarbonDayOfWeek($currentDate->dayOfWeek);
             $isWeekend = in_array($dayOfWeek->value, $weekendDays);

             $daysToInsert[] = [
                 'academic_year_id' => $term->academic_year_id,
                 'academic_term_id' => $term->id,
                 'date' => $currentDate->toDateString(),
                 'status' => $isWeekend ? SchoolDayType::Holiday->value : SchoolDayType::SchoolDay->value,
                 'day_part' => DayPartEnum::FULL_DAY->value,
                 'notes' => null,
                 'created_at' => now(),
                 'updated_at' => now(),
             ];

             $currentDate->addDay();
         }

         // Bulk insert if there are days to insert
         if (! empty($daysToInsert)) {
             SchoolDay::insert($daysToInsert);
         }
    }

    /**
     * Sync school days when academic term dates are updated.
     */
    public function syncDaysForTerm(AcademicTerm $term): void
    {
        DB::transaction(function () use ($term) {
            // Get the old dates from database (before update)
            $oldTerm = AcademicTerm::find($term->id);
            if (! $oldTerm) {
                return;
            }

            $oldStartDate = Carbon::parse($oldTerm->start_date);
            $oldEndDate = Carbon::parse($oldTerm->end_date);
            $newStartDate = Carbon::parse($term->start_date);
            $newEndDate = Carbon::parse($term->end_date);

            // Get weekend days from settings
            $weekendDays = school()->getArrayData('weekend_days', []);
            if (! is_array($weekendDays)) {
                $weekendDays = [];
            }

            // Handle expansion (new days added)
            if ($newEndDate->gt($oldEndDate)) {
                // Add new days from old end date to new end date
                $this->addDaysForDateRange(
                    $term,
                    $oldEndDate->copy()->addDay(),
                    $newEndDate,
                    $weekendDays
                );
            }

            if ($newStartDate->lt($oldStartDate)) {
                // Add new days from new start date to old start date
                $this->addDaysForDateRange(
                    $term,
                    $newStartDate->copy(),
                    $oldStartDate->copy()->subDay(),
                    $weekendDays
                );
            }

            // Handle shrinking (days to be removed)
            if ($newStartDate->gt($oldStartDate) || $newEndDate->lt($oldEndDate)) {
                // Find days that are now outside the new range
                $daysToCheck = SchoolDay::where('academic_term_id', $term->id)
                    ->where(function ($query) use ($newStartDate, $newEndDate) {
                        $query->where('date', '<', $newStartDate)
                            ->orWhere('date', '>', $newEndDate);
                    })
                    ->whereHas('attendanceSheets')
                    ->get();

                // Check each day for attendance records
                foreach ($daysToCheck as $day) {
                    if ($day->hasAttendanceRecords()) {
                        $dateString = Carbon::parse($day->date)->format('Y-m-d');
                        throw new \Exception(
                            "لا يمكن تعديل الترم لأن هناك سجلات حضور مرتبطة باليوم: {$dateString}. يرجى حذف سجلات الحضور أولاً."
                        );
                    }
                }

                // Delete days that don't have attendance records
                SchoolDay::where('academic_term_id', $term->id)
                    ->where(function ($query) use ($newStartDate, $newEndDate) {
                        $query->where('date', '<', $newStartDate)
                            ->orWhere('date', '>', $newEndDate);
                    })
                    ->whereDoesntHave('attendanceSheets')
                    ->delete();
            }
        });
    }

    /**
     * Add school days for a specific date range.
     */
    private function addDaysForDateRange(AcademicTerm $term, Carbon $startDate, Carbon $endDate, array $weekendDays): void
    {
        $daysToInsert = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Check if day already exists
            $existingDay = SchoolDay::where('academic_year_id', $term->academic_year_id)
                ->where('date', $currentDate->toDateString())
                ->first();

            if ($existingDay) {
                // Update existing day with academic_term_id if not set
                if (! $existingDay->academic_term_id) {
                    $existingDay->update(['academic_term_id' => $term->id]);
                }
                $currentDate->addDay();

                continue;
            }

            // Check if it's a weekend day
            $dayOfWeek = DayOfWeekEnum::fromCarbonDayOfWeek($currentDate->dayOfWeek);
            $isWeekend = in_array($dayOfWeek->value, $weekendDays);

            $daysToInsert[] = [
                'academic_year_id' => $term->academic_year_id,
                'academic_term_id' => $term->id,
                'date' => $currentDate->toDateString(),
                'status' => $isWeekend ? SchoolDayType::Holiday->value : SchoolDayType::SchoolDay->value,
                'day_part' => DayPartEnum::FULL_DAY->value,
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $currentDate->addDay();
        }

        // Bulk insert if there are days to insert
        if (! empty($daysToInsert)) {
            SchoolDay::insert($daysToInsert);
        }
    }
}
