<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {
    /** Site copy by dot-notation key, e.g. setting('hero.subtitle'). */
    function setting(string $key, mixed $default = null): mixed
    {
        return data_get(Setting::allCached(), $key, $default);
    }
}

if (! function_exists('media_url')) {
    /** Public URL for an upload on the "public" disk; absolute URLs (e.g. a hosted video) pass through. */
    function media_url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }
        if (preg_match('#^(https?:)?//#', $path)) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}

if (! function_exists('marked')) {
    /**
     * Escapes admin copy, then turns *text* into emphasis, so headings can be edited as plain text:
     * marked('A developer who thinks like a *product owner*.') wraps "product owner" in <span class="hl">.
     */
    function marked(?string $text, string $tag = 'hl'): Illuminate\Support\HtmlString
    {
        [$open, $close] = match ($tag) {
            'hl' => ['<span class="hl">', '</span>'],
            default => ["<{$tag}>", "</{$tag}>"],
        };

        return new Illuminate\Support\HtmlString(
            preg_replace('/\*(.+?)\*/s', $open . '$1' . $close, e((string) $text))
        );
    }
}

if (! function_exists('unmarked')) {
    /** The same copy as plain text (for <title>, meta tags and structured data). */
    function unmarked(?string $text): string
    {
        return trim(str_replace('*', '', (string) $text));
    }
}
