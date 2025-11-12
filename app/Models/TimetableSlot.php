<?php

namespace App\Models;

use App\Enums\DayOfWeekEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableSlot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'timetable_id',
        'teacher_assignment_id',
        'day_of_week',
        'period_number',
        'duration_minutes',
    ];

    /**
     * Get the casts for the model.
     */
    protected function casts(): array
    {
        return [
            'day_of_week' => DayOfWeekEnum::class,
        ];
    }

    /**
     * Get the timetable that owns this slot.
     */
    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function timetableSetting()
    {
        return $this->hasOneThrough(
            TimetableSetting::class,
            Timetable::class,
            'id',
            'id',
            'timetable_id',
            'timetable_setting_id',
        );
    }

    /**
     * Get the teacher assignment for this slot.
     */
    public function teacherAssignment(): BelongsTo
    {
        return $this->belongsTo(TeacherAssignment::class);
    }
}
