<?php

namespace App\Models;

use App\Models\Concerns\Ordered;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StackItem extends Model
{
    use Ordered;

    protected $guarded = ['id'];

    protected $casts = ['is_featured' => 'boolean'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(StackGroup::class, 'stack_group_id');
    }
}
