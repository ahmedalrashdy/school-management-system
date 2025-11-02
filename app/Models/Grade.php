<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use Cachable;
    use HasSortOrder;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'stage_id', 'sort_order'];

    /**
     * Get the stage that owns the grade.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    /**
     * Get the sections for the grade.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get the curriculums for the grade.
     */
    public function curriculums(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    /**
     * Get the enrollments for the grade.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Check if the grade can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return ! $this->sections()->exists()
            && ! $this->curriculums()->exists()
            && ! $this->enrollments()->exists();
    }

    /**
     * Get the reason why the grade cannot be deleted.
     */
    public function getDeletionBlockReason(): ?string
    {
        $reasons = [];

        if ($this->sections()->exists()) {
            $reasons[] = 'شعب دراسية';
        }

        if ($this->curriculums()->exists()) {
            $reasons[] = 'مناهج دراسية';
        }

        if ($this->enrollments()->exists()) {
            $reasons[] = 'تسجيلات طلاب';
        }

        if (empty($reasons)) {
            return null;
        }

        return 'لا يمكن حذف هذا الصف لوجود: '.implode('، ', $reasons).' مرتبطة به.';
    }
}
