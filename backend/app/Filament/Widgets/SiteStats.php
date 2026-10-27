<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\GalleryItem;
use App\Models\Service;
use App\Models\Testimonial;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteStats extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        // daily inquiry counts for the last 30 days, oldest first (grouped in PHP so it works on SQLite and MySQL)
        $since = now()->subDays(29)->startOfDay();
        $perDay = ContactMessage::where('created_at', '>=', $since)->pluck('created_at')
            ->countBy(fn ($date) => $date->format('Y-m-d'));
        $trend = collect(range(0, 29))->map(fn ($i) => $perDay[$since->copy()->addDays($i)->format('Y-m-d')] ?? 0);

        $thisMonth = ContactMessage::where('created_at', '>=', now()->startOfMonth())->count();
        $lastMonth = ContactMessage::whereBetween('created_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->startOfMonth()])->count();
        $unread = ContactMessage::whereNull('read_at')->count();

        $change = match (true) {
            $lastMonth === 0 && $thisMonth === 0 => 'No inquiries yet this month',
            $lastMonth === 0 => 'First inquiries this month',
            default => sprintf('%+d%% vs last month', round(($thisMonth - $lastMonth) / $lastMonth * 100)),
        };

        return [
            Stat::make('Inquiries · 30 days', $trend->sum())
                ->description($change)
                ->descriptionIcon($thisMonth >= $lastMonth ? Heroicon::ArrowTrendingUp : Heroicon::ArrowTrendingDown)
                ->chart($trend->all())
                ->color('primary'),
            Stat::make('Unread', $unread)
                ->description($unread ? 'Reply within ' . setting('contact.reply_time') : 'All caught up')
                ->descriptionIcon($unread ? Heroicon::Envelope : Heroicon::CheckCircle)
                ->color($unread ? 'warning' : 'success'),
            Stat::make('Services live', Service::published()->count())
                ->description(Service::count() - Service::published()->count() . ' hidden · each has its own page')
                ->descriptionIcon(Heroicon::Briefcase),
            Stat::make('Reviews · photos', Testimonial::published()->count() . ' · ' . GalleryItem::published()->count())
                ->description('Published on the site')
                ->descriptionIcon(Heroicon::Star),
        ];
    }
}
