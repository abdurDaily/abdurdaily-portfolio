<?php

namespace App\Filament\Resources\ContactMessages;

use App\Models\ContactMessage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Inquiries sent through the site's contact form (read-only). */
class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $modelLabel = 'message';

    protected static ?string $navigationLabel = 'Inbox';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|UnitEnum|null $navigationGroup = 'Inbox';

    public static function getNavigationBadge(): ?string
    {
        $unread = ContactMessage::whereNull('read_at')->count();

        return $unread ? (string) $unread : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextEntry::make('name'),
            TextEntry::make('created_at')->label('Received')->dateTime(),
            TextEntry::make('email')->copyable(),
            TextEntry::make('phone')->placeholder('—')->copyable(),
            TextEntry::make('message')->columnSpanFull()->prose(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()
                    ->weight(fn (ContactMessage $record) => $record->read_at ? FontWeight::Normal : FontWeight::Bold)
                    ->description(fn (ContactMessage $record) => $record->email),
                TextColumn::make('message')->limit(70)->color('gray')->searchable(),
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')->label('Read')->nullable(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markRead')->label('Mark read')->icon(Heroicon::OutlinedCheck)
                    ->visible(fn (ContactMessage $record) => ! $record->read_at)
                    ->action(fn (ContactMessage $record) => $record->update(['read_at' => now()])),
                Action::make('reply')->icon(Heroicon::OutlinedEnvelope)
                    ->url(fn (ContactMessage $record) => 'mailto:' . $record->email . '?subject=' . rawurlencode('Re: your project inquiry')),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageContactMessages::route('/')];
    }
}
