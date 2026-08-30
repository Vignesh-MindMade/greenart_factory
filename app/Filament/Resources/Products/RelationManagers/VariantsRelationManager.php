<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Variants (detail page blocks)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)           // fires the callback when admin clicks away
                            ->afterStateUpdated(function (string $operation, ?string $state, $set) {
                                if ($operation === 'create') {  // only auto-fill on CREATE, not EDIT
                                    $set('slug', Str::slug($state));
                                }
                                // On edit: admin may have custom slug — don't overwrite it
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                fn ($livewire, $record): \Illuminate\Validation\Rules\Unique =>
                                    Rule::unique('product_variants', 'slug')
                                        ->where('product_id', $livewire->getOwnerRecord()->id)
                                        //                    ↑ scope the check to THIS product only
                                        ->ignore($record?->id),
                                        //        ↑ ignore current record on edit
                            ]),
                    ]),

                Textarea::make('description')
                    ->rows(5)
                    ->columnSpanFull()
                    ->helperText('Body copy shown beside the block image on the product detail page.'),

                Grid::make(2)
                    ->schema([
                        TextInput::make('display_no')
                            ->label('Display number')
                            ->maxLength(8)
                            ->placeholder('01')
                            ->helperText('Large numeral beside the heading. Free text — the design reuses values.'),
                        TextInput::make('sort_order')
                            ->label('Order on detail page')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Specification card')
                    ->description('The small card beside each block — one label/value pair plus attribute chips.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('spec_label')
                                    ->label('Label')
                                    ->maxLength(255)
                                    ->placeholder('Material'),
                                TextInput::make('spec_value')
                                    ->label('Value')
                                    ->maxLength(255)
                                    ->placeholder('100% Preserved Natural Moss'),
                            ]),
                        TagsInput::make('spec_tags')
                            ->label('Attribute chips')
                            ->placeholder('Add a chip')
                            ->helperText('e.g. Biophilic, Eco-Certified, No Water')
                            ->columnSpanFull(),
                    ]),

                Section::make('Linked projects')
                    ->description('Drives the "View project" link on this block.')
                    ->schema([
                        Select::make('portfolioProjects')
                            ->label('Portfolio projects')
                            ->relationship('portfolioProjects', 'title')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('variant_images')
                            ->collection('variant_images')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxFiles(10)
                            ->preserveFilenames()
                            ->label('Variant Images')
                            ->helperText('The first image is used as the block image on the detail page.'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('display_no')
                    ->label('#')
                    ->badge(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('portfolio_projects_count')
                    ->label('Projects')
                    ->counts('portfolioProjects')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
