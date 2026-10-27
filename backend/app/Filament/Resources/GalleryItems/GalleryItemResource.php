<?php

namespace App\Filament\Resources\GalleryItems;

use App\Models\GalleryItem;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class GalleryItemResource extends Resource
{
    protected static ?string $model = GalleryItem::class;

    protected static ?string $modelLabel = 'photo';

    protected static ?string $navigationLabel = 'Gallery';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            FileUpload::make('image')->label('Full-size photo')->image()->disk('public')->directory('gallery')->required(),
            FileUpload::make('thumbnail')->label('Thumbnail (optional)')->image()->disk('public')->directory('gallery')
                ->helperText('A smaller copy for the grid; the full photo is used if empty.'),
            TextInput::make('alt')->label('Alt text')->required()->maxLength(255)->columnSpanFull()
                ->helperText('Describe what is in the photo — read by screen readers and search engines.'),
            TextInput::make('kicker')->maxLength(255)->placeholder('Convocation 2026'),
            TextInput::make('title')->maxLength(255),
            TextInput::make('caption')->label('Viewer caption')->maxLength(255),
            Select::make('size')->options(GalleryItem::SIZES)->default('standard')->required()->native(false),
            Toggle::make('is_published')->label('Published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('thumbnail')->disk('public')->defaultImageUrl(fn (GalleryItem $record) => media_url($record->image)),
                TextColumn::make('title')->description(fn (GalleryItem $record) => $record->kicker),
                TextColumn::make('size')->badge()->formatStateUsing(fn (string $state) => GalleryItem::SIZES[$state] ?? $state),
                IconColumn::make('is_published')->label('Published')->boolean(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageGalleryItems::route('/')];
    }
}
