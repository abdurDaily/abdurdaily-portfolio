<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\InquiriesChart;
use App\Filament\Widgets\LatestInquiries;
use App\Filament\Widgets\SeoHealth;
use App\Filament\Widgets\SiteStats;
use App\Filament\Widgets\WelcomeBanner;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            WelcomeBanner::class,
            SiteStats::class,
            InquiriesChart::class,
            SeoHealth::class,
            LatestInquiries::class,
        ];
    }

    public function getColumns(): int|array
    {
        return ['default' => 1, 'lg' => 3];
    }
}
