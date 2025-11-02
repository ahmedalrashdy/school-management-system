<?php

namespace App\Models;

use App\Enums\AcademicYearStatus;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curriculum extends Model
{
    // use Cachable;

    protected $table = 'curriculums';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['academic_year_id', 'grade_id', 'academic_term_id'];

    /**
     * Get the academic year that owns the curriculum.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the grade that owns the curriculum.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the academic term that owns the curriculum.
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Get the subjects for the curriculum.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'curriculum_subject')
            ->withTimestamps();
    }

    /**
     * Get the curriculum_subject pivot records.
     */
    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class);
    }

    /**
     * Check if the curriculum belongs to an archived academic year.
     */
    public function belongsToArchivedYear(): bool
    {
        return $this->academicYear && $this->academicYear->status === AcademicYearStatus::Archived;
    }

    public function canAddSubject(): bool
    {
        return $this->academicTerm->is_active || $this->academicYear->status == AcademicYearStatus::Upcoming;
    }

    /**
     * Check if the curriculum can be deleted.
     */
    public function canBeDeleted(): bool
    {
        // يمكن حذف المنهج، لكن قد نضيف حماية إضافية
        return true;
    }
}
