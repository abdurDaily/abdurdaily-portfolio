<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'message', 'ip', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];
}
