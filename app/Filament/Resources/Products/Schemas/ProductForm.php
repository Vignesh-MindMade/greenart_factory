<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required() ->maxLength(255)
                    ->live(onBlur: true)           // fires the callback when admin clicks away
                    ->afterStateUpdated(function (string $operation, ?string $state, $set) {
                    if ($operation === 'create') {  // only auto-fill on CREATE, not EDIT
                        $set('slug', Str::slug($state));
                    }
                    // On edit: admin may have custom slug — don't overwrite it
                }),
                TextInput::make('slug')
                    ->required() ->maxLength(255)
                    ->unique(ignoreRecord: true),
                    
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
