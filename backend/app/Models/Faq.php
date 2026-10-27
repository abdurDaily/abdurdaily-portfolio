<?php

namespace App\Models;

use App\Models\Concerns\Ordered;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use Ordered;

    protected $guarded = ['id'];

    protected $casts = ['is_published' => 'boolean'];
}
