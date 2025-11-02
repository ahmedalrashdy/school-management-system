<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
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
     * Get the curriculums that include this subject.
     */
    public function curriculums(): BelongsToMany
    {
        return $this->belongsToMany(Curriculum::class, 'curriculum_subject')
            ->withTimestamps();
    }

    /**
     * Get the curriculum_subject pivot records.
     */
    public function curriculumSubjects()
    {
        return $this->hasMany(CurriculumSubject::class);
    }

    /**
     * Check if the subject can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->curriculumSubjects()->count() === 0;
    }

    /**
     * Get the reason why the subject cannot be deleted.
     */
    public function getDeletionBlockReason(): ?string
    {
        $count = $this->curriculumSubjects()->count();

        if ($count > 0) {
            return 'لا يمكن حذف هذه المادة لأنها جزء من '.$count.' منهج دراسي على الأقل.';
        }

        return null;
    }
}
