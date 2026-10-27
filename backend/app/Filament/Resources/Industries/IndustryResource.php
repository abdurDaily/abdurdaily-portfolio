<?php

namespace App\Filament\Resources\Industries;

use App\Models\Industry;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class IndustryResource extends Resource
{
    protected static ?string $model = Industry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('tags')->maxLength(255)->placeholder('stores · marketplaces'),
            TextInput::make('icon')->maxLength(255)->placeholder('bi-bag')
                ->helperText('A Bootstrap Icons name — see icons.getbootstrap.com.'),
            FileUpload::make('image')->image()->disk('public')->directory('industries')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('image')->disk('public'),
                TextColumn::make('title'),
                TextColumn::make('tags')->color('gray'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageIndustries::route('/')];
    }
}
