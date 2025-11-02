<?php

namespace App\Models;

use App\Enums\AcademicYearStatus;
use App\Traits\HasAcademicScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasAcademicScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'capacity', 'academic_year_id', 'grade_id', 'academic_term_id'];

    /**
     * Get the academic year that owns the section.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Get the grade that owns the section.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the students for the section through section_students pivot table.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'section_students')
            ->withTimestamps();
    }

    /**
     * Get the teacher assignments for the section.
     */
    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    /**
     * Get the timetables for the section.
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * Get the current number of students in the section.
     */
    public function getCurrentStudentsCountAttribute(): int
    {
        return $this->students()->count();
    }

    /**
     * Get the current students count (for eager loading).
     */
    public function currentStudentsCount(): int
    {
        return $this->students_count ?? $this->students()->count();
    }

    public function gradingRules()
    {
        return $this->hasMany(GradingRule::class);
    }

    /**
     * Check if the section can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->students()->count() === 0
            && $this->teacherAssignments()->count() === 0
            && $this->timetables()->count() === 0;
    }

    /**
     * Get the reason why the section cannot be deleted.
     */
    public function getDeletionBlockReason(): ?string
    {
        $reasons = [];

        if ($this->students()->count() > 0) {
            $reasons[] = 'طلاب مسجلين';
        }

        if ($this->teacherAssignments()->count() > 0) {
            $reasons[] = 'تعيينات مدرسين';
        }

        if ($this->timetables()->count() > 0) {
            $reasons[] = 'جداول دراسية';
        }

        if (empty($reasons)) {
            return null;
        }

        return 'لا يمكن حذف هذه الشعبة لوجود: '.implode('، ', $reasons).' مرتبطة بها. يرجى إزالة الارتباطات أولاً.';
    }

    /**
     * Check if the section belongs to an archived academic year.
     */
    public function belongsToArchivedYear(): bool
    {
        return $this->academicYear && $this->academicYear->status === AcademicYearStatus::Archived;
    }

    /**
     * Get the active timetable for this section.
     */
    public function activeTimetable(): ?Timetable
    {
        return $this->timetables()->where('is_active', true)->first();
    }
}
