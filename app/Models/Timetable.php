<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Timetable extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'section_id',
        'timetable_setting_id',
        'is_active',
    ];

    /**
     * Get the casts for the model.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the section that owns this timetable.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the timetable setting that owns this timetable.
     */
    public function timetableSetting(): BelongsTo
    {
        return $this->belongsTo(TimetableSetting::class);
    }

    /**
     * Get the timetable slots for this timetable.
     */
    public function slots(): HasMany
    {
        return $this->hasMany(TimetableSlot::class);
    }

    /**
     * Get the class sessions for this timetable.
     */
    public function attendanceSheets(): HasManyThrough
    {
        return $this->hasManyThrough(AttendanceSheet::class, TimetableSlot::class);
    }

    /**
     * Check if the timetable can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return ! $this->is_active && $this->attendanceSheets()->count() === 0;
    }

    /**
     * Get slots grouped by day and period.
     */
    public function getSlotsGrouped()
    {
        return $this->slots
            ->groupBy('day_of_week')
            ->map(function ($daySlots) {
                return $daySlots->keyBy('period_number');
            });
    }

    /**
     * Get the reason why the timetable cannot be deleted.
     */
    public function getDeletionBlockReason(): ?string
    {
        if ($this->is_active) {
            return 'لا يمكن حذف الجدول النشط. يرجى تعطيله أولاً.';
        }

        $sessionsCount = $this->attendanceSheets()->count();
        if ($sessionsCount > 0) {
            return 'لا يمكن حذف الجدول لأنه مرتبط بسجل حضور ';
        }

        return null;
    }
}
