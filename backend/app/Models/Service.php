<?php

namespace App\Models;

use App\Models\Concerns\Ordered;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Ordered;

    protected $guarded = ['id'];

    protected $casts = ['is_published' => 'boolean'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
