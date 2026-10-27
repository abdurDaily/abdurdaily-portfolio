<?php

namespace App\Filament\Resources\Testimonials;

use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $modelLabel = 'review';

    protected static ?string $navigationLabel = 'Client reviews';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Client')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('role')->maxLength(255)->placeholder('Founder'),
                TextInput::make('company')->maxLength(255),
                TextInput::make('country')->maxLength(255),
                Textarea::make('quote')->required()->rows(4)->columnSpanFull(),
                FileUpload::make('avatar')->label('Photo')->image()->avatar()->disk('public')->directory('testimonials')
                    ->helperText('Optional — initials are shown without one.'),
            ]),
            Section::make('Video review')->columns(2)->schema([
                Select::make('video_type')->label('Video')->native(false)->live()->default('none')->required()
                    ->options(['none' => 'No video', 'file' => 'Video file URL', 'youtube' => 'YouTube']),
                TextInput::make('video')->maxLength(255)
                    ->label(fn (Get $get) => $get('video_type') === 'youtube' ? 'YouTube video ID' : 'Video URL (.mp4)')
                    ->placeholder(fn (Get $get) => $get('video_type') === 'youtube' ? 'dQw4w9WgXcQ' : 'https://…/review.mp4')
                    ->required(fn (Get $get) => $get('video_type') !== 'none')
                    ->visible(fn (Get $get) => $get('video_type') !== 'none'),
                FileUpload::make('poster')->label('Video cover image')->image()->disk('public')->directory('testimonials')
                    ->helperText('Defaults to the client photo.')
                    ->visible(fn (Get $get) => $get('video_type') !== 'none'),
                TextInput::make('project')->maxLength(255)->placeholder('Analytics dashboard')
                    ->helperText('Shown on the client card when there is no video.')
                    ->visible(fn (Get $get) => $get('video_type') === 'none'),
                TextInput::make('note')->maxLength(255)->placeholder('Hired again for two more projects')
                    ->visible(fn (Get $get) => $get('video_type') === 'none'),
            ]),
            Toggle::make('is_published')->label('Published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('avatar')->disk('public')->circular(),
                TextColumn::make('name')->searchable()->description(fn (Testimonial $record) => $record->byline()),
                TextColumn::make('quote')->limit(60)->color('gray'),
                IconColumn::make('is_published')->label('Published')->boolean(),
            ])
            ->recordActions([EditAction::make()->slideOver(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageTestimonials::route('/')];
    }
}
