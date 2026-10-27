<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/** Content shown in an admin-controlled order (the `sort` column), with optional publishing. */
trait Ordered
{
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
