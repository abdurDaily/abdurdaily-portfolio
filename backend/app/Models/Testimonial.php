<?php

namespace App\Models;

use App\Models\Concerns\Ordered;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use Ordered;

    protected $guarded = ['id'];

    protected $casts = ['is_published' => 'boolean'];

    public function hasVideo(): bool
    {
        return $this->video_type !== 'none' && filled($this->video);
    }

    /** Value for the media viewer's data-src: a YouTube ID, or a playable URL. */
    public function videoSource(): ?string
    {
        return $this->video_type === 'youtube' ? $this->video : media_url($this->video);
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim($this->name)))
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }

    /** "Founder, ShopNest · USA" */
    public function byline(): string
    {
        $role = collect([$this->role, $this->company])->filter()->implode(', ');

        return collect([$role, $this->country])->filter()->implode(' · ');
    }
}
