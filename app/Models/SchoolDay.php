<?php

namespace App\Models;

use App\Enums\DayOfWeekEnum;
use App\Enums\DayPartEnum;
use App\Enums\SchoolDayType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolDay extends Model
{
    protected $fillable = [
        'academic_year_id',
        'academic_term_id',
        'date',
        'status',
        'day_part',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => SchoolDayType::class,
            'day_part' => DayPartEnum::class,
        ];
    }

    /**
     * Get the academic year that owns the school day.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }


    /**
     * Get the academic term that owns the school day.
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Get the attendance sheets for this school day.
     */
    public function attendanceSheets(): HasMany
    {
        return $this->hasMany(AttendanceSheet::class);
    }

    public function getIsSchoolDayAttribute(): bool
    {
        return $this->status->value === SchoolDayType::SchoolDay->value;
    }

    public function getIsPartialHolidayAttribute(): bool
    {
        return $this->status->value === SchoolDayType::PartialHoliday->value;
    }

    public function getIsHolidayAttribute(): bool
    {
        return $this->status->value === SchoolDayType::Holiday->value;
    }

    public function IsWeekendDay(array $weekendDays): bool
    {

        return in_array(DayOfWeekEnum::fromCarbonDayOfWeek($this->date->dayOfWeek)->value, $weekendDays);
    }

    /**
     * Check if this day has any attendance records.
     */
    public function hasAttendanceRecords(): bool
    {
        return $this->attendanceSheets()->exists();
    }
}
