<?php

namespace App\Models;

use App\Models\Concerns\Ordered;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use Ordered;

    /** Bento tile shapes; each maps to a gallery__item--{size} modifier in style.css. */
    public const SIZES = ['standard' => 'Standard', 'tall' => 'Tall (portrait)', 'wide' => 'Wide', 'half' => 'Half width'];

    protected $guarded = ['id'];

    protected $casts = ['is_published' => 'boolean'];
}
