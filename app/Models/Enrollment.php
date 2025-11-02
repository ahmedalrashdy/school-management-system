<?php

namespace App\Models;

use App\Enums\ActivityLogNameEnum;
use App\Traits\HasModelLabels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Enrollment extends Model
{
    use HasModelLabels, LogsActivity;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'grade_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(ActivityLogNameEnum::Academics->value)
            ->logOnly(['grade_id', 'academic_year_id', 'student_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the student that owns the enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic year for the enrollment.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the grade for the enrollment.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }
}
