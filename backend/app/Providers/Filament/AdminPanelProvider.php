<?php

namespace App\Providers\Filament;

use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            // brand: same palette and type as the public site (see public/assets/css/admin.css)
            ->brandName('abdurDaily')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('1.6rem')
            ->favicon("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%23121210'/%3E%3Ctext x='50%25' y='56%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial,sans-serif' font-weight='700' font-size='30' fill='%23ff5a1f'%3EaD%3C/text%3E%3C/svg%3E")
            ->colors([
                'primary' => Color::hex('#ff5a1f'),
                'gray' => Color::Stone,
            ])
            ->font('Geist', provider: GoogleFontProvider::class)
            ->monoFont('Geist Mono', provider: GoogleFontProvider::class)
            ->serifFont('Instrument Serif', provider: GoogleFontProvider::class)
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => new HtmlString(
                '<link rel="stylesheet" href="' . asset('assets/css/admin.css') . '?v=' . filemtime(public_path('assets/css/admin.css')) . '">'
            ))
            ->navigationItems([
                NavigationItem::make('View site')
                    ->url(fn () => route('home'), shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->sort(100),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
