<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;


class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()->maxLength(255)
                    ->live(onBlur:true)
                    ->afterStateUpdated(function(string $operation, ?string $state, $set){
                        if ($operation === 'create') {  // only auto-fill on CREATE, not EDIT
                        $set('slug', Str::slug($state));
                    }
                    }),
                TextInput::make('slug')
                    ->required()->maxLength(255)
                    ->unique(ignoreRecord:true),
                Select::make('status')
                    ->required()
                    ->default('draft')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived'
                    ]),
                  
                        
            ]);
    }
}
