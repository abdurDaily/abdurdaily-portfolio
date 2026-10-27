<?php

namespace App\Filament\Resources\StackGroups;

use App\Models\StackGroup;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class StackGroupResource extends Resource
{
    protected static ?string $model = StackGroup::class;

    protected static ?string $modelLabel = 'tech stack group';

    protected static ?string $navigationLabel = 'Tech stack';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('subtitle')->maxLength(255),
            Repeater::make('items')->relationship()->orderColumn('sort')->columnSpanFull()
                ->itemLabel(fn (array $state) => $state['name'] ?? null)->collapsible()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('description')->maxLength(255),
                    TextInput::make('icon')->maxLength(255)
                        ->helperText('Devicon or Bootstrap Icons class, e.g. "devicon-laravel-original colored" or "bi bi-shield-check".'),
                    Toggle::make('is_featured')->label('Show in hero "Core stack"')->inline(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('subtitle')->color('gray'),
                TextColumn::make('items_count')->counts('items')->label('Tools'),
            ])
            ->recordActions([EditAction::make()->slideOver(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageStackGroups::route('/')];
    }
}
