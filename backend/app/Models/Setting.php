<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value site copy. Read it with the setting('hero.subtitle') helper;
 * every key is loaded once and cached until a setting changes.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = ['value' => 'array'];

    public static function allCached(): array
    {
        return Cache::rememberForever('settings', fn () => static::pluck('value', 'key')->all());
    }

    /** Replaces the values of the given top-level keys. */
    public static function store(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget('settings');
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings'));
        static::deleted(fn () => Cache::forget('settings'));
    }
}
