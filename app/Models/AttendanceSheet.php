<?php

namespace App\Models;

use App\Enums\DayPartEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSheet extends Model
{
    protected $fillable = [
        'school_day_id',
        'section_id',
        'day_part',
        'timetable_slot_id',
        'taken_by',
        'updated_by',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'day_part' => DayPartEnum::class,
            'locked_at' => 'datetime',
        ];
    }

    /**
     * Get the school day that owns the attendance sheet.
     */
    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class);
    }

    /**
     * Get the section that owns the attendance sheet.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the user who took the attendance.
     */
    public function takenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    /**
     * Get the user who last updated the attendance.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the timetable slot for this attendance sheet.
     */
    public function timetableSlot(): BelongsTo
    {
        return $this->belongsTo(TimetableSlot::class);
    }

    /**
     * Get the attendances for this attendance sheet.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'attendance_sheet_id');
    }

    /**
     * Check if this attendance sheet is for PerPeriod mode.
     */
    public function isPerPeriodMode(): bool
    {
        return $this->timetable_slot_id !== null;
    }

    /**
     * Check if this attendance sheet is for Daily mode.
     */
    public function isDailyMode(): bool
    {
        return $this->timetable_slot_id === null && $this->day_part?->value === \App\Enums\DayPartEnum::FULL_DAY->value;
    }

    /**
     * Check if this attendance sheet is for SplitDaily mode.
     */
    public function isSplitDailyMode(): bool
    {
        return $this->timetable_slot_id === null && $this->day_part !== null && $this->day_part->value !== \App\Enums\DayPartEnum::FULL_DAY->value;
    }

    /**
     * Check if this attendance sheet is locked.
     */
    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    /**
     * Lock this attendance sheet.
     */
    public function lock(): void
    {
        $this->update(['locked_at' => now()]);
    }

    /**
     * Unlock this attendance sheet.
     */
    public function unlock(): void
    {
        $this->update(['locked_at' => null]);
    }

    /**
     * Get display type for this attendance sheet.
     */
    public function getDisplayType(): string
    {
        if ($this->isPerPeriodMode()) {
            return 'حصة دراسية';
        }

        if ($this->isSplitDailyMode()) {
            return $this->day_part?->label() ?? 'فترة';
        }

        return 'يوم كامل';
    }

    /**
     * Get subject name if this is a PerPeriod mode attendance sheet.
     */
    public function getSubjectName(): ?string
    {
        if (! $this->isPerPeriodMode() || ! $this->timetableSlot) {
            return null;
        }

        return $this->timetableSlot
            ->teacherAssignment
            ->curriculumSubject
            ->subject
            ->name ?? null;
    }
}
