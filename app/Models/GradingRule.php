<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradingRule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section_id',
        'curriculum_subject_id',
        'coursework_max_marks',
        'final_exam_max_marks',
        'passed_mark',
        'total_marks',
        'final_exam_id',
        'is_published',
    ];

    /**
     * Get the casts for the model.
     */
    protected function casts(): array
    {
        return [
            'passed_mark' => 'decimal:2',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Get the section that owns the grading rule.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function curriculumSubject(): BelongsTo
    {
        return $this->belongsTo(CurriculumSubject::class);
    }

    /**
     * Get the final exam that owns the grading rule.
     */
    public function finalExam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'final_exam_id');
    }

    /**
     * Get the grading rule items for this rule.
     */
    public function items(): HasMany
    {
        return $this->hasMany(GradingRuleItem::class);
    }
}
