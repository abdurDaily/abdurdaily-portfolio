<?php

namespace App\Models;

use App\Models\Concerns\Ordered;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use Ordered;

    protected $guarded = ['id'];
}
