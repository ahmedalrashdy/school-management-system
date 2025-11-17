<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mark extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'exam_id',
        'marks_obtained',
        'notes',
    ];

    /**
     * Get the casts for the model.
     */
    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
        ];
    }

    /**
     * Get the student that owns the mark.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the exam that owns the mark.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
