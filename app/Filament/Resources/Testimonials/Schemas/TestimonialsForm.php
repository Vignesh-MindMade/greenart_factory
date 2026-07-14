<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;

class TestimonialsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->schema([
                        TextInput::make('customer_name')
                            ->required(),
                        TextInput::make('customer_title'),
                    ]),
             
                Section::make('Additional Information')
                    ->schema([
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Select::make('status')
                            ->required()
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived'=>'Archived'
                            ])
                             ->default('draft'),
                    ]),
                          Section::make('Media')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('testimonial_image')
                                ->collection('testimonial_images')
                                ->imagePreviewHeight('200')
                                ->required()
                                ->preserveFilenames()
                                ->columnSpanFull(),
                            
                        ]),

                       Textarea::make('quote')
                    ->required()
                    ->columnSpanFull(),

              
                   
            ]);
    }
}
