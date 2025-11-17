<?php

namespace App\Models;

use App\Enums\AcademicYearStatus;
use App\Traits\HasAcademicScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'academic_year_id',
        'academic_term_id',
        'exam_type_id',
        'curriculum_subject_id',
        'section_id',
        'exam_date',
        'max_marks',
        'is_final',
    ];

    /**
     * Get the casts for the model.
     */
    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'is_final' => 'bool',
        ];
    }

    /**
     * Get the academic year that owns the exam.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the academic term that owns the exam.
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Get the exam type that owns the exam.
     */
    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    /**
     * Get the curriculum subject that owns the exam.
     */
    public function curriculumSubject(): BelongsTo
    {
        return $this->belongsTo(CurriculumSubject::class);
    }

    /**
     * Get the section that owns the exam.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the marks for this exam.
     */
    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Check if the exam has any marks recorded.
     */
    public function hasMarks(): bool
    {
        return $this->marks()->count() > 0;
    }

    /**
     * Check if the exam can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return !$this->hasMarks();
    }

    /**
     * Get the reason why the exam cannot be deleted.
     */
    public function getDeletionBlockReason(): ?string
    {
        if ($this->hasMarks()) {
            $count = $this->marks()->count();

            return 'لا يمكن حذف هذا الامتحان لوجود ' . $count . ' درجة مرصودة للطلاب. لحذف الامتحان، يجب أولاً حذف جميع الدرجات المرتبطة به.';
        }

        return null;
    }

    /**
     * Check if the exam can be edited.
     */
    public function canBeEdited(): bool
    {
        return !$this->hasMarks();
    }

    /**
     * Check if marks can be entered for this exam.
     * Marks can only be entered for exams in Active or Upcoming academic years.
     */
    public function canEnterMarks(): bool
    {
        if (!$this->academicYear) {
            return false;
        }

        return in_array($this->academicYear->status, [
            AcademicYearStatus::Active,
            AcademicYearStatus::Upcoming,
        ], true);
    }
}
