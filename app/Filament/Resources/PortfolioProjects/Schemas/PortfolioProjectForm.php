<?php

namespace App\Filament\Resources\PortfolioProjects\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\PortfolioProject;
use App\Services\GalleryService;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;


class PortfolioProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true) // fires the callback when admin clicks away
                    ->afterStateUpdated(function (string $operation, ?string $state, $set) {
                        if ($operation === 'create') {
                            // only auto-fill on CREATE, not EDIT
                            $set('slug', Str::slug($state));
                        }
                        // On edit: admin may have custom slug — don't overwrite it
                    }),
                TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            ]),
            Grid::make(2)->schema([Select::make('category_id')->relationship('category', 'name')->searchable()->preload()->required(), Select::make('location_id')->relationship('location', 'name')->searchable()->preload()->required()]),

            Grid::make(2)->schema([Select::make('sectors')->relationship('sectors', 'name')->multiple()->searchable()->preload(), Select::make('installationTypes')->relationship('installationTypes', 'name')->multiple()->searchable()->preload()]),
         Select::make('productVariants')
    ->relationship(
        name: 'productVariants',
        titleAttribute: 'name',
        modifyQueryUsing: fn ($query) => $query->with('product')
        //                ↑ same closure, different home
    )
    ->multiple()
    ->searchable()
    ->preload()
    ->getOptionLabelFromRecordUsing(
        fn ($record) => $record->product->name . ' → ' . $record->name
    ),
            Select::make('status')
                ->required()
                ->default('draft')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ]),
                Grid::make(2)->schema([
    Toggle::make('is_featured')
        ->label('Show on Homepage')
        ->default(false),
    TextInput::make('featured_order')
        ->numeric()
        ->default(0)
        ->label('Homepage Display Order'),
]),


Section::make('Case study')
    ->description('Body copy for the project detail page.')
    ->schema([
        Textarea::make('excerpt')
            ->rows(3)
            ->maxLength(500)
            ->columnSpanFull()
            ->helperText('Short blurb on the portfolio listing card.'),

        RichEditor::make('execution')
            ->label('Our execution')
            ->columnSpanFull()
            ->helperText('Opening paragraph of the execution section.'),

        RichEditor::make('key_stages')
            ->label('Key stages')
            ->columnSpanFull()
            ->helperText('Bulleted list of stages. Use the list buttons rather than typing bullet characters.'),

        RichEditor::make('key_highlights')
            ->label('Key highlights')
            ->columnSpanFull(),
    ]),

// BRD FR-2.3 asks for dedicated Challenge and Solution fields. The design
// renders them as one "our challenge & solution" block — the frontend joins
// them, so both requirements are met without losing the structure.
Section::make('Challenge & solution')
    ->description('Stored separately per BRD FR-2.3; rendered as one block on the site.')
    ->schema([
        RichEditor::make('challenge')
            ->label('Challenge faced')
            ->columnSpanFull(),
        RichEditor::make('solution')
            ->label('Solution provided')
            ->columnSpanFull(),
    ]),

// The dark card beside the challenge & solution block. Its label/value rows
// are managed in the Specification rows tab, after saving.
Section::make('Specification card')
    ->description('Dark card on the project detail page. Rows are managed in the tab below.')
    ->collapsed()
    ->schema([
        TextInput::make('spec_eyebrow')
            ->label('Eyebrow')
            ->maxLength(255)
            ->placeholder('MOSS WALL INSTALLATION – COMMERCIAL PROJECT')
            ->columnSpanFull(),
        Textarea::make('spec_headline')
            ->label('Headline')
            ->rows(2)
            ->maxLength(500)
            ->placeholder('This bespoke moss wall creates a calming green backdrop for a modern office workspace.')
            ->columnSpanFull(),
        RichEditor::make('spec_body')
            ->label('Body copy')
            ->columnSpanFull()
            ->helperText('Paragraphs below the specification rows.'),
    ]),

Section::make('Media')
    ->schema([
        SpatieMediaLibraryFileUpload::make('cover_image')
            ->collection('cover_image')     // ← matches registerMediaCollections name
            ->image()
            ->imagePreviewHeight('200')
            ->maxFiles(1)
            ->directory('Cover')
            ->label('Cover Image (archive thumbnail)')
            ->preserveFilenames(),

        SpatieMediaLibraryFileUpload::make('project_images')
            ->collection('project_images')  // ← matches registerMediaCollections name
            ->multiple()
            ->reorderable()                 // drag to set display order
            ->image()
            ->maxFiles(20)
             ->preserveFilenames()
            ->label('Project Gallery Images'),
    ]),

// BRD section 6 — images from a published project appear automatically in the
// gallery of every collection it is tagged to. Untick one to keep it off those
// pages (internal or before-work shots). Nothing is re-uploaded either way.
Section::make('Product gallery sync')
    ->description('Which of this project\'s images may appear on linked collection gallery pages.')
    ->collapsed()
    ->visibleOn('edit')
    ->schema([
        CheckboxList::make('gallery_visible_media')
            ->label('Show in collection galleries')
            ->options(fn (?PortfolioProject $record): array => $record
                ? $record->getMedia('cover_image')
                    ->concat($record->getMedia('project_images'))
                    ->mapWithKeys(fn ($media) => [$media->id => $media->name ?: $media->file_name])
                    ->toArray()
                : [])
            ->afterStateHydrated(function (CheckboxList $component, ?PortfolioProject $record): void {
                if (! $record) {
                    return;
                }

                // Default is shown, so only an explicit false opts an image out.
                $component->state(
                    $record->getMedia('cover_image')
                        ->concat($record->getMedia('project_images'))
                        ->filter(fn ($media) => $media->getCustomProperty(GalleryService::SHOW_IN_GALLERY, true) !== false)
                        ->pluck('id')
                        ->map(fn ($id) => (string) $id)
                        ->toArray()
                );
            })
            ->dehydrated(false)   // persisted by EditPortfolioProject::afterSave()
            ->bulkToggleable()
            ->columns(2)
            ->helperText('Unticked images stay on this project page but are hidden from gallery pages.'),
    ]),
                
        ]);
    }
}
