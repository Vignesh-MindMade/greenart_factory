<?php

namespace App\Filament\Resources\Certifications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('standard_code')
                    ->required()
                    ->helperText('e.g. "ISO 9001", "ISO 14001"'),
                TextInput::make('title'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->required()
                    ->default('draft')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
                Section::make('Logo')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('certification_logos')
                            ->collection('certification_logos')
                            ->image()
                            ->imagePreviewHeight('150')
                            ->preserveFilenames(),
                    ]),
            ]);
    }
}
