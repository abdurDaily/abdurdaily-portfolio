<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use Filament\Widgets\ChartWidget;

class InquiriesChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected ?string $heading = 'Inquiries';

    protected ?string $description = 'Contact-form messages per month, last 12 months';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 2];

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $start = now()->startOfMonth()->subMonths(11);
        $perMonth = ContactMessage::where('created_at', '>=', $start)->pluck('created_at')
            ->countBy(fn ($date) => $date->format('Y-m'));
        $months = collect(range(0, 11))->map(fn ($i) => $start->copy()->addMonths($i));

        return [
            'datasets' => [[
                'label' => 'Inquiries',
                'data' => $months->map(fn ($m) => $perMonth[$m->format('Y-m')] ?? 0)->all(),
                'fill' => 'start',
                'tension' => 0.35,
                'pointRadius' => 3,
            ]],
            'labels' => $months->map(fn ($m) => $m->format('M'))->all(),
        ];
    }

    protected ?string $pollingInterval = null;

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                'x' => ['grid' => ['display' => false]],
            ],
        ];
    }
}
