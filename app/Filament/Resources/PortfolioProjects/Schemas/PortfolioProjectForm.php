<?php

namespace App\Filament\Resources\PortfolioProjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Grid;

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
            Select::make('productVariants')->relationship('productVariants', 'name')->multiple()->searchable()->preload()->getOptionLabelFromRecordUsing(
                fn($record) => $record->product->name . ' → ' . $record->name,
                // Shows: "Moss Creations → Moss wall" in the dropdown
                //        "Green Walls → Living Green Wall"
            ),
            Select::make('status')
                ->required()
                ->default('draft')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ]),
        ]);
    }
}
