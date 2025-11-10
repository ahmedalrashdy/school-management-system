<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherAssignment extends Model
{
    protected $fillable = [
        'curriculum_subject_id',
        'teacher_id',
        'section_id',
    ];

    /**
     * Get the curriculum subject for this assignment.
     */
    public function curriculumSubject(): BelongsTo
    {
        return $this->belongsTo(CurriculumSubject::class);
    }

    /**
     * Get the teacher for this assignment.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the section for this assignment.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }


    public function timetableSlots(): HasMany
    {
        return $this->hasMany(TimetableSlot::class);
    }
}
