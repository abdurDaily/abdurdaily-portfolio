<?php

namespace App\Filament\Resources\Services;

use App\Models\Service;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Service')->columns(2)->schema([
                TextInput::make('title')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state, string $operation) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')->required()->maxLength(255)->alphaDash()->unique(ignoreRecord: true)
                    ->helperText('The page address: /services/your-slug. Changing it breaks existing links.'),
                Textarea::make('summary')->required()->maxLength(300)->rows(2)->columnSpanFull()
                    ->helperText('One line, shown in the services list.'),
                FileUpload::make('image')->image()->disk('public')->directory('services')->columnSpanFull()
                    ->helperText('Shown on the service page and as its social-share image.'),
                RichEditor::make('body')->label('Page content')->columnSpanFull()
                    ->toolbarButtons(['h2', 'h3', 'bold', 'italic', 'link', 'bulletList', 'orderedList', 'blockquote', 'undo', 'redo']),
            ]),
            Section::make('Search engines')->columns(1)->collapsible()->schema([
                TextInput::make('meta_title')->maxLength(255)
                    ->helperText('Optional. Defaults to "Title | Name — site". Aim for under 60 characters.'),
                Textarea::make('meta_description')->maxLength(320)->rows(2)
                    ->helperText('Optional. Defaults to the summary. Aim for 120–160 characters.'),
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
                ImageColumn::make('image')->disk('public'),
                TextColumn::make('title')->searchable()->description(fn (Service $record) => "/services/{$record->slug}"),
                IconColumn::make('is_published')->label('Published')->boolean(),
            ])
            ->recordActions([
                Action::make('view')->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Service $record) => route('services.show', $record), shouldOpenInNewTab: true),
                EditAction::make()->slideOver(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageServices::route('/')];
    }
}
