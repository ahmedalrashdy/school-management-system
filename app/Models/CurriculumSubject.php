<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CurriculumSubject extends Model
{
    // use Cachable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'curriculum_subject';


    protected $touches = ['subject'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['curriculum_id', 'subject_id'];

    /**
     * Get the curriculum that owns the curriculum subject.
     */
    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    /**
     * Get the subject that owns the curriculum subject.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher assignments for this curriculum subject.
     */
    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class, 'curriculum_subject_id');
    }

    public function GradingRules(): HasMany
    {
        return $this->hasMany(GradingRule::class);
    }

    public function GradingRule(): HasOne
    {
        return $this->hasOne(GradingRule::class);
    }

    /**
     * Get the exams for this curriculum subject.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'curriculum_subject_id');
    }

    /**
     * Check if the curriculum subject can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->teacherAssignments()->count() === 0
            && $this->exams()->count() === 0;
    }

    /**
     * Get the reason why the curriculum subject cannot be deleted.
     */
    public function getDeletionBlockReason(): ?string
    {
        $reasons = [];

        if ($this->teacherAssignments()->count() > 0) {
            $reasons[] = 'تعيينات مدرسين';
        }

        if ($this->exams()->count() > 0) {
            $reasons[] = 'امتحانات';
        }

        if (empty($reasons)) {
            return null;
        }

        return 'لا يمكن إزالة هذه المادة من المنهج لوجود: ' . implode(' و', $reasons) . ' مرتبطة بها. يرجى حذف تلك الارتباطات أولاً.';
    }
}
