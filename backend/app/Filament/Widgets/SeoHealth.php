<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Storage;

/** A checklist of the SEO basics the admin controls, each linking to where it is fixed. */
class SeoHealth extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.seo-health';

    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 1];

    protected function getViewData(): array
    {
        $settings = SiteSettings::getUrl();
        $title = mb_strlen(unmarked(setting('site.meta_title')));
        $description = mb_strlen((string) setting('site.meta_description'));
        $image = setting('site.og_image');
        $services = Service::published()->get();
        $withMeta = $services->filter(fn (Service $s) => filled($s->meta_description))->count();

        $checks = [
            [
                'ok' => app()->isProduction(),
                'label' => 'Visible to search engines',
                'detail' => app()->isProduction() ? 'Indexing is on' : 'Off on this ' . app()->environment() . ' copy — on automatically in production',
                'url' => null,
            ],
            [
                'ok' => $title >= 30 && $title <= 60,
                'label' => 'Home page title',
                'detail' => "{$title} characters · ideal 30–60",
                'url' => $settings,
            ],
            [
                'ok' => $description >= 120 && $description <= 160,
                'label' => 'Meta description',
                'detail' => "{$description} characters · ideal 120–160",
                'url' => $settings,
            ],
            [
                'ok' => filled($image) && Storage::disk('public')->exists($image),
                'label' => 'Social share image',
                'detail' => filled($image) ? 'Set' : 'Missing — links shared without a preview',
                'url' => $settings,
            ],
            [
                'ok' => filled(setting('site.google_verification')),
                'label' => 'Google Search Console',
                'detail' => filled(setting('site.google_verification')) ? 'Verified' : 'Add your verification code',
                'url' => $settings,
            ],
            [
                'ok' => $services->isNotEmpty() && $withMeta === $services->count(),
                'label' => 'Service page descriptions',
                'detail' => "{$withMeta} of {$services->count()} written (others use the summary)",
                'url' => ServiceResource::getUrl(),
            ],
        ];

        $passed = collect($checks)->where('ok', true)->count();

        return [
            'checks' => $checks,
            'score' => (int) round($passed / count($checks) * 100),
            'passed' => $passed,
            'total' => count($checks),
            'sitemap' => route('sitemap'),
            'pages' => $services->count() + 1,
        ];
    }
}
