<?php

namespace App\Filament\Resources\Services\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;



class ServiceitemRelationManager extends RelationManager
{
    protected static string $relationship = 'serviceitem';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur:true)
                    ->afterStateUpdated(function (string $operation, ?string $state, $set) { 
                        if ($operation === 'create') {  // only auto-fill on CREATE, not EDIT
                        $set('slug', Str::slug($state));
                    }} ),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),

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
                        ])
                        ->columnSpan('full'),
                                    
            ]);

    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
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
