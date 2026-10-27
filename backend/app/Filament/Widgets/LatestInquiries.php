<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestInquiries extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?string $heading = 'Latest inquiries';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(ContactMessage::query()->latest()->limit(5))
            ->paginated(false)
            ->emptyStateHeading('No inquiries yet')
            ->emptyStateDescription('Messages sent through the contact form will appear here.')
            ->emptyStateIcon(Heroicon::OutlinedInbox)
            ->columns([
                TextColumn::make('name')
                    ->weight(fn (ContactMessage $record) => $record->read_at ? FontWeight::Normal : FontWeight::SemiBold)
                    ->description(fn (ContactMessage $record) => $record->email),
                TextColumn::make('message')->limit(80)->color('gray'),
                TextColumn::make('read_at')->label('Status')->badge()
                    ->state(fn (ContactMessage $record) => $record->read_at ? 'Read' : 'New')
                    ->color(fn (string $state) => $state === 'New' ? 'primary' : 'gray'),
                TextColumn::make('created_at')->label('Received')->since(),
            ])
            ->headerActions([
                Action::make('all')->label('Open inbox')->link()->url(ContactMessageResource::getUrl()),
            ])
            ->recordActions([
                Action::make('reply')->icon(Heroicon::OutlinedEnvelope)->color('gray')
                    ->url(fn (ContactMessage $record) => 'mailto:' . $record->email . '?subject=' . rawurlencode('Re: your project inquiry')),
            ]);
    }
}
