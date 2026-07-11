<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;

class HeroSlidesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('headline')
                    ->required(),
                TextInput::make('subtext'),
                TextInput::make('cta_label')
                    ->required(),
                TextInput::make('cta_url')
                    ->url()
                    ->required(),
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
                    SpatieMediaLibraryFileUpload::make('hero_slide_images')
                        ->collection('hero_slide_images')
                        ->imagePreviewHeight('200')
                        ->maxFiles(1)
                        ->image()
                        ->required()
                         ->preserveFilenames(),
                ])
            ]);
    }
}
