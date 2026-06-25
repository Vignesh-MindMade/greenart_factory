<?php

namespace App\Filament\Resources\Products\RelationManagers;

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

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()  ->maxLength(255)
    ->live(onBlur: true)
    ->afterStateUpdated(function (string $operation, ?string $state, $set) {
        if ($operation === 'create') {
            $set('slug', Str::slug($state));
        }
    }),
                TextColumn::make('slug')
                    ->searchable()->maxLength(255)
                    ->rules([
        fn ($livewire, $record): \Illuminate\Validation\Rules\Unique =>
            Rule::unique('product_variants', 'slug')
                ->where('product_id', $livewire->getOwnerRecord()->id)
                //                    ↑ scope the check to THIS product only
                ->ignore($record?->id),
                //        ↑ ignore current record on edit
    ]),
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
