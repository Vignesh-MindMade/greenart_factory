<?php

namespace App\Filament\Resources\CertificationBadges\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificationBadgeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('subtitle')
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
                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('certification_badge_images')
                            ->collection('certification_badge_images')
                            ->imagePreviewHeight('200')
                            ->preserveFilenames(),
                    ]),
            ]);
    }
}
