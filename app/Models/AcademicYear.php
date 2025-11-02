<?php

namespace App\Models;

use App\Enums\AcademicYearStatus;
use App\Enums\ActivityLogNameEnum;
use App\Traits\HasModelLabels;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AcademicYear extends Model
{
    use Cachable, HasModelLabels, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(ActivityLogNameEnum::Academics->value)
            ->dontSubmitEmptyLogs()
            ->logAll();
    }

    protected static function booted()
    {
        $handler = function ($model) {
            school()->clearCache();
        };

        static::saved($handler);
        static::updated($handler);
        static::deleted($handler);
    }

    protected $fillable = ['name', 'start_date', 'end_date', 'status'];

    protected function casts(): array
    {
        return [
            'status' => AcademicYearStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function academicTerms(): HasMany
    {
        return $this->hasMany(AcademicTerm::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', AcademicYearStatus::Active->value);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', AcademicYearStatus::Upcoming->value);
    }

    public function scopeActiveAndUpcomimgOnly($query)
    {
        return $query->whereIn('status', [AcademicYearStatus::Active->value, AcademicYearStatus::Upcoming->value]);
    }

    public function teacherAssignments(): HasManyThrough
    {
        return $this->hasManyThrough(TeacherAssignment::class, Section::class);
    }
}
