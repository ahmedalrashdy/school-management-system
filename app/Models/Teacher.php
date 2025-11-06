<?php

namespace App\Models;

use App\Enums\AcademicQualificationEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'specialization',
        'qualification',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'qualification' => AcademicQualificationEnum::class,
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
     * Get the teacher assignments.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function timetableSlots(): HasManyThrough
    {
        return $this->hasManyThrough(TimetableSlot::class, TeacherAssignment::class);
    }
}
