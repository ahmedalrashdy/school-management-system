<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamType extends Model
{
    use Cachable;
    use HasSortOrder;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'sort_order'];

    /**
     * Get the exams for this exam type.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Check if the exam type can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->exams()->count() === 0;
    }

    /**
     * Get the reason why the exam type cannot be deleted.
     */
    public function getDeletionBlockReason(): ?string
    {
        $count = $this->exams()->count();

        if ($count > 0) {
            return 'لا يمكن حذف هذا النوع لوجود '.$count.' امتحان مرتبط به على الأقل.';
        }

        return null;
    }
}
