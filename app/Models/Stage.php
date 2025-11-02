<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
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
     * Get the grades for the stage.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Check if the stage has any grades.
     */
    public function hasGrades(): bool
    {
        return $this->grades()->exists();
    }
}
