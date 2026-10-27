<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\ContactMessage;
use Filament\Widgets\Widget;

/** Greeting in the owner's time zone, availability status and quick links. */
class WelcomeBanner extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.welcome-banner';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $now = now(setting('contact.timezone', config('app.timezone')));

        return [
            'greeting' => match (true) {
                $now->hour < 5 => 'Working late',
                $now->hour < 12 => 'Good morning',
                $now->hour < 17 => 'Good afternoon',
                default => 'Good evening',
            },
            'firstName' => str(auth()->user()->name)->before(' ') ?: auth()->user()->name,
            'now' => $now,
            'city' => setting('contact.city'),
            'available' => (bool) setting('contact.available'),
            'unread' => ContactMessage::whereNull('read_at')->count(),
            'links' => [
                'content' => SiteSettings::getUrl(),
                'services' => ServiceResource::getUrl(),
                'inbox' => ContactMessageResource::getUrl(),
                'site' => route('home'),
            ],
        ];
    }
}
