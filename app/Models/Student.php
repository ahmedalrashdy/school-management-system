<?php

namespace App\Models;

use App\Enums\ActivityLogNameEnum;
use App\Traits\HasModelLabels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Student extends Model
{
    use HasFactory, HasModelLabels, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(ActivityLogNameEnum::Academics->value)
            ->logOnly(['admission_number', 'date_of_birth', 'city', 'district'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function logFormats(): array
    {
        return (new static)->getCasts();
    }

    protected $fillable = [
        'user_id',
        'admission_number',
        'date_of_birth',
        'city',
        'district',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Get the owning user profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the guardians related to this student.
     */
    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student')
            ->withPivot('relation_to_student')
            ->withTimestamps();
    }

    /**
     * Get the enrollments for the student.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the sections for the student through section_students pivot table.
     */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'section_students')
            ->withTimestamps();
    }

    /**
     * Get the current section for the student in the active academic year.
     */
    public function currentSection(): ?Section
    {
        $activeYear = school()->activeYear();
        if (! $activeYear) {
            return null;
        }

        return $this->sections()
            ->where('academic_year_id', $activeYear->id)
            ->latest('section_students.created_at')
            ->first();
    }

    public function termsInGrade(int $gradeId)
    {
        return AcademicTerm::whereHas('sections', function ($q) use ($gradeId) {
            $q->where('grade_id', $gradeId);
            $q->whereHas('students', function ($q) {
                $q->where('students.id', $this->id);
            });
        });
    }

    /**
     * Get the grades for this student .
     */
    public function grades(): BelongsToMany
    {

        return $this->belongsToMany(Grade::class, Enrollment::class)
            ->withPivot(['academic_year_id']);
    }

    public function academicYears(): BelongsToMany
    {
        return $this->belongsToMany(AcademicYear::class, Enrollment::class)
            ->withPivot(['grade_id']);
    }

    /**
     * Get the marks for this student.
     */
    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Get the attendances for this student.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceSheets()
    {
        return $this->belongsToMany(
            AttendanceSheet::class,
            'attendances',
            'student_id',
            'attendance_sheet_id'
        )
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Check if the student has any marks in a specific academic year and term.
     */
    public function hasMarksInTerm(int $academicYearId, int $academicTermId): bool
    {
        return $this->marks()
            ->whereHas('exam', function ($query) use ($academicYearId, $academicTermId) {
                $query->where('academic_year_id', $academicYearId)
                    ->where('academic_term_id', $academicTermId);
            })
            ->exists();
    }
}
