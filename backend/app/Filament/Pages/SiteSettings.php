<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Edits every single-value piece of site copy (hero, about, SEO, contact details, section headings…).
 * Each top-level key (site, hero, about…) is stored as one row in `settings`.
 *
 * @property-read Schema $form
 */
class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $navigationLabel = 'Site content';

    protected static ?string $title = 'Site content';

    protected static ?int $navigationSort = -1;

    private const HIGHLIGHT_HINT = 'Wrap words in *asterisks* to highlight them.';

    /** @var array<string, mixed> | null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::allCached());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Tabs::make()->persistTabInQueryString()->tabs([
                $this->seoTab(),
                $this->contactTab(),
                $this->heroTab(),
                $this->aboutTab(),
                $this->sectionsTab(),
                $this->processTab(),
                $this->contactSectionTab(),
            ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save changes')->submit('save')->keyBindings(['mod+s']),
                    ])->sticky(),
                ]),
        ]);
    }

    public function save(): void
    {
        Setting::store($this->form->getState());

        Notification::make()->success()->title('Saved — the site is updated.')->send();
    }

    private function seoTab(): Tab
    {
        return Tab::make('SEO & identity')->icon(Heroicon::OutlinedMagnifyingGlass)->schema([
            Section::make('Identity')->columns(3)->schema([
                TextInput::make('site.name')->label('Brand / site name')->required(),
                TextInput::make('site.person')->label('Your name')->required(),
                TextInput::make('site.job_title')->required(),
            ]),
            Section::make('Search results & social sharing')->columns(2)->schema([
                TextInput::make('site.meta_title')->label('Home page title')->required()->maxLength(70)->columnSpanFull()
                    ->helperText('Shown as the blue link in Google. Keep it under ~60 characters, most important words first.'),
                Textarea::make('site.meta_description')->label('Home page description')->required()->maxLength(320)->rows(3)->columnSpanFull()
                    ->helperText('The snippet under the link in Google. Aim for 120–160 characters.'),
                FileUpload::make('site.og_image')->label('Social share image')->image()->disk('public')->directory('site')
                    ->helperText('Shown when the site is shared on LinkedIn, Facebook, X, WhatsApp… Ideal size 1200×630.'),
                TextInput::make('site.twitter')->label('X / Twitter handle')->placeholder('@handle'),
                TextInput::make('site.google_verification')->label('Google Search Console verification code')
                    ->helperText('Only the content="…" value of the HTML-tag verification method.'),
            ]),
        ]);
    }

    private function contactTab(): Tab
    {
        return Tab::make('Contact & social')->icon(Heroicon::OutlinedAtSymbol)->schema([
            Section::make('Contact details')->columns(3)->schema([
                TextInput::make('contact.email')->email()->required(),
                TextInput::make('contact.phone')->label('Phone (as displayed)')->required(),
                TextInput::make('contact.whatsapp')->label('WhatsApp number')->required()->regex('/^\d{6,15}$/')
                    ->helperText('Digits only, with country code: 8801632212661'),
                TextInput::make('contact.city')->required(),
                TextInput::make('contact.country')->required(),
                TextInput::make('contact.country_code')->label('Country code')->required()->length(2)->placeholder('BD'),
                Select::make('contact.timezone')->label('Time zone (live clock)')->required()->searchable()
                    ->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list())),
                TextInput::make('contact.reply_time')->label('Reply time')->required()->placeholder('12h'),
            ]),
            Section::make('Availability')->columns(2)->schema([
                Toggle::make('contact.available')->label('Open for new work')->inline(false),
                TextInput::make('contact.available_text')->label('Hero badge text'),
            ]),
            Section::make('Social profiles')->schema([
                Repeater::make('socials')->hiddenLabel()->columns(4)->reorderable()->collapsible()
                    ->itemLabel(fn (array $state) => $state['label'] ?? null)
                    ->schema([
                        TextInput::make('label')->required(),
                        TextInput::make('url')->url()->required(),
                        TextInput::make('icon')->required()->placeholder('bi-github')->helperText('Bootstrap Icons name'),
                        Toggle::make('in_hero')->label('Show in hero')->inline(false),
                    ]),
            ]),
        ]);
    }

    private function heroTab(): Tab
    {
        return Tab::make('Hero')->icon(Heroicon::OutlinedSparkles)->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('hero.greeting')->required(),
                TextInput::make('hero.sign')->label('Signature line')->helperText(self::HIGHLIGHT_HINT),
                Textarea::make('hero.subtitle')->required()->rows(2)->columnSpanFull()->helperText('Wrap words in *asterisks* to make them bold.'),
                TextInput::make('hero.primary_cta')->label('Primary button')->required(),
                TextInput::make('hero.secondary_cta')->label('Secondary button')->required(),
                FileUpload::make('hero.image')->label('Portrait (cut-out)')->image()->disk('public')->directory('site')->required(),
                TextInput::make('hero.image_alt')->label('Portrait alt text')->required(),
                TextInput::make('hero.stack_title')->label('"Core stack" heading')
                    ->helperText('Choose which tools appear under Tech stack → "Show in hero".'),
                TextInput::make('hero.contact_title')->label('Contact card heading'),
            ]),
            Section::make('Stats')->schema([
                Repeater::make('hero.stats')->hiddenLabel()->columns(3)->reorderable()->maxItems(4)
                    ->schema([
                        TextInput::make('value')->required(),
                        TextInput::make('suffix')->placeholder('+'),
                        TextInput::make('label')->required(),
                    ]),
            ]),
            Section::make('Brands strip')->schema([
                TextInput::make('brands.title')->label('Heading'),
                Repeater::make('brands.items')->label('Brands')->columns(2)->reorderable()->grid(2)
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('icon')->placeholder('bi-hexagon-fill'),
                    ]),
            ]),
        ]);
    }

    private function aboutTab(): Tab
    {
        return Tab::make('About')->icon(Heroicon::OutlinedUser)->schema([
            Section::make()->columns(2)->schema([
                ...$this->headingFields('about', description: false),
                Textarea::make('about.lead')->required()->rows(3)->columnSpanFull(),
                Textarea::make('about.body')->rows(4)->columnSpanFull()->helperText('Leave an empty line between paragraphs.'),
                TextInput::make('about.availability')->label('Availability text'),
                TextInput::make('about.primary_cta')->label('Primary button'),
                TextInput::make('about.secondary_cta')->label('Secondary button'),
            ]),
            Section::make('Key facts')->schema([
                Repeater::make('about.facts')->hiddenLabel()->columns(2)->reorderable()
                    ->schema([TextInput::make('label')->required(), TextInput::make('value')->required()]),
            ]),
            Section::make('How I work')->schema([
                TextInput::make('about.principles_title')->label('Heading'),
                Repeater::make('about.principles')->label('Principles')->reorderable()->grid(2)
                    ->schema([TextInput::make('title')->required(), Textarea::make('text')->required()->rows(2)]),
            ]),
        ]);
    }

    private function sectionsTab(): Tab
    {
        return Tab::make('Section headings')->icon(Heroicon::OutlinedBars3BottomLeft)->schema([
            Section::make('Services')->columns(2)->collapsible()->schema($this->headingFields('services_section')),
            Section::make('Tech stack')->columns(2)->collapsible()->schema($this->headingFields('stack_section')),
            Section::make('Moving words band')->collapsible()->schema([
                Repeater::make('outline.words')->hiddenLabel()->reorderable()->grid(3)->schema([TextInput::make('word')->required()]),
            ]),
            Section::make('Industries')->columns(2)->collapsible()->schema([
                TextInput::make('industries_section.title')->label('Heading')->helperText(self::HIGHLIGHT_HINT),
                TextInput::make('industries_section.card_cta')->label('Card hover text'),
            ]),
            Section::make('Gallery')->columns(2)->collapsible()->schema($this->headingFields('gallery_section')),
            Section::make('Client reviews')->columns(2)->collapsible()->schema($this->headingFields('reviews_section')),
            Section::make('FAQ')->columns(2)->collapsible()->schema([
                ...$this->headingFields('faq_section'),
                TextInput::make('faq_section.cta')->label('Button'),
            ]),
        ]);
    }

    private function processTab(): Tab
    {
        return Tab::make('Process & promise')->icon(Heroicon::OutlinedArrowPath)->schema([
            Section::make('Process')->columns(2)->schema([
                ...$this->headingFields('process'),
                Repeater::make('process.steps')->label('Steps')->columnSpanFull()->reorderable()->columns(3)
                    ->itemLabel(fn (array $state) => $state['title'] ?? null)->collapsible()
                    ->schema([
                        TextInput::make('title')->required(),
                        TextInput::make('icon')->required()->placeholder('bi-search'),
                        TextInput::make('status')->placeholder('✓ passed'),
                        Textarea::make('text')->required()->rows(2)->columnSpanFull(),
                    ]),
            ]),
            Section::make('Promise (dark band)')->columns(2)->schema([
                ...$this->headingFields('statement', description: false),
                Repeater::make('statement.points')->label('Code points')->columnSpanFull()->columns(2)->reorderable()->maxItems(3)
                    ->helperText('Rendered as: const name = "value";')
                    ->schema([TextInput::make('name')->required(), TextInput::make('value')->required()]),
            ]),
        ]);
    }

    private function contactSectionTab(): Tab
    {
        return Tab::make('Contact & footer')->icon(Heroicon::OutlinedEnvelope)->schema([
            Section::make('Contact section')->columns(2)->schema([
                TextInput::make('contact_section.label')->label('Label'),
                TextInput::make('contact_section.title')->label('Heading')->helperText('Wrap words in *asterisks* to italicise them.'),
                Textarea::make('contact_section.subtitle')->rows(2)->columnSpanFull(),
                Textarea::make('contact_section.form_intro')->label('Text beside the form')->rows(2),
                Textarea::make('contact_section.success')->label('Message after sending')->rows(2),
            ]),
            Section::make('Footer')->columns(2)->schema([
                TextInput::make('footer.cta')->label('Call to action'),
                TextInput::make('footer.tagline'),
                Repeater::make('footer.links')->label('Extra links')->columnSpanFull()->columns(3)
                    ->schema([
                        TextInput::make('label')->required(),
                        TextInput::make('url')->url()->required(),
                        Toggle::make('sponsored')->label('Affiliate / sponsored')->inline(false),
                    ]),
            ]),
        ]);
    }

    /** Label, cue, heading and (optionally) description fields for a numbered section heading. */
    private function headingFields(string $key, bool $description = true): array
    {
        return array_filter([
            TextInput::make("{$key}.label")->label('Label'),
            TextInput::make("{$key}.cue")->label('Cue (italic, after the label)'),
            TextInput::make("{$key}.title")->label('Heading')->required()->columnSpanFull()->helperText(self::HIGHLIGHT_HINT),
            $description ? Textarea::make("{$key}.description")->label('Description')->rows(2)->columnSpanFull() : null,
        ]);
    }
}
