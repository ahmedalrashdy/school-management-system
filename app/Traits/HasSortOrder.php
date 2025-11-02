<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSortOrder
{
    /**
     * Scope a query to order results by sort_order.
     */
    public function scopeSorted(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('sort_order', $direction);
    }
}
