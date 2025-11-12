<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimetableSetting extends Model
{
    use Cachable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_active',
        'periods_per_day',
        'first_period_start_time',
        'default_period_duration_minutes',
        'periods_before_break',
        'break_duration_minutes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'periods_per_day' => 'array',
        'first_period_start_time' => 'datetime',
    ];

    /**
     * Get the timetables that use this setting.
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * Check if the setting can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->timetables()->count() === 0;
    }

    /**
     * Get the reason why the setting cannot be deleted.
     */
    public function getDeletionBlockReason(): ?string
    {
        $count = $this->timetables()->count();

        if ($count > 0) {
            return 'لا يمكن حذف هذا القالب لأنه مستخدم في '.$count.' جدول دراسي على الأقل.';
        }

        return null;
    }
}
