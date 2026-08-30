<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VarietiesRelationManager extends RelationManager
{
    protected static string $relationship = 'varieties';

    protected static ?string $title = 'Varieties (texture grid)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Flat Moss')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                // Unique per product, mirroring product_variants.
                                fn ($livewire, $record): \Illuminate\Validation\Rules\Unique =>
                                    Rule::unique('product_varieties', 'slug')
                                        ->where('product_id', $livewire->getOwnerRecord()->id)
                                        ->ignore($record?->id),
                            ]),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextInput::make('label')
                            ->label('Eyebrow label')
                            ->maxLength(255)
                            ->placeholder('Variety 01')
                            ->helperText('Small text above the name.'),
                        TextInput::make('sort_order')
                            ->label('Order in grid')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Textarea::make('description')
                    ->rows(4)
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('variety_image')
                            ->collection('variety_image')
                            ->image()
                            ->imagePreviewHeight('200')
                            ->maxFiles(1)
                            ->preserveFilenames()
                            ->label('Variety image'),
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
                SpatieMediaLibraryImageColumn::make('variety_image')
                    ->collection('variety_image')
                    ->label('')
                    ->height(40),
                TextColumn::make('label')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(60)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
