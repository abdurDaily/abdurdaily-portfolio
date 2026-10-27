<?php

namespace App\Models;

use App\Models\Concerns\Ordered;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StackGroup extends Model
{
    use Ordered;

    protected $guarded = ['id'];

    public function items(): HasMany
    {
        return $this->hasMany(StackItem::class)->orderBy('sort')->orderBy('id');
    }
}
