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
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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

    protected static ?string $modelLabel = 'gallery item';

    protected static ?string $navigationLabel = 'Gallery';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        $isVideo = fn (Get $get) => $get('kind') === 'video';

        return $schema->columns(1)->components([
            Section::make('Media')->columns(2)->schema([
                // Not a column: derived from whether a video is set, and clears the video when switched back to Photo.
                ToggleButtons::make('kind')->label('Type')->inline()->live()->dehydrated(false)->columnSpanFull()
                    ->options(['photo' => 'Photo', 'video' => 'Video'])
                    ->icons(['photo' => Heroicon::OutlinedPhoto, 'video' => Heroicon::OutlinedVideoCamera])
                    ->afterStateHydrated(fn (ToggleButtons $component, ?GalleryItem $record) => $component->state($record?->hasVideo() ? 'video' : 'photo'))
                    ->afterStateUpdated(fn (?string $state, Set $set) => $state === 'photo' ? $set('video', null) : null),
                FileUpload::make('video')->label('Video file')->disk('public')->directory('gallery')->columnSpanFull()
                    ->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(40 * 1024)
                    ->helperText('MP4 or WebM, up to 40 MB. Landscape (16:9) clips look best on a Wide tile.')
                    ->required($isVideo)->visible($isVideo)->dehydratedWhenHidden(),
                FileUpload::make('image')->image()->disk('public')->directory('gallery')->required()
                    ->label(fn (Get $get) => $isVideo($get) ? 'Cover image' : 'Full-size photo')
                    ->helperText(fn (Get $get) => $isVideo($get) ? 'Shown on the tile and before the video plays.' : null),
                FileUpload::make('thumbnail')->label('Thumbnail (optional)')->image()->disk('public')->directory('gallery')
                    ->helperText('A smaller copy for the grid; the full image is used if empty.'),
            ]),
            Section::make('Details')->columns(2)->schema([
                TextInput::make('alt')->label('Alt text')->required()->maxLength(255)->columnSpanFull()
                    ->helperText('Describe what is shown — read by screen readers and search engines.'),
                TextInput::make('kicker')->maxLength(255)->placeholder('Convocation 2026'),
                TextInput::make('title')->maxLength(255),
                TextInput::make('caption')->label('Viewer caption')->maxLength(255),
                Select::make('size')->options(GalleryItem::SIZES)->default('standard')->required()->native(false),
                Toggle::make('is_published')->label('Published')->default(true),
            ]),
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
                TextColumn::make('kind')->label('Type')->badge()
                    ->state(fn (GalleryItem $record) => $record->hasVideo() ? 'Video' : 'Photo')
                    ->color(fn (string $state) => $state === 'Video' ? 'warning' : 'gray'),
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
