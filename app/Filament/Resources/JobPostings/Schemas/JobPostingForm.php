<?php

namespace App\Filament\Resources\JobPostings\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class JobPostingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, ?string $state, $set) {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        }),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                ]),

                Grid::make(3)->schema([
                    TextInput::make('department')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('location')
                        ->required()
                        ->maxLength(255),
                    Select::make('employment_type')
                        ->required()
                        ->default('full_time')
                        ->options([
                            'full_time' => 'Full-time',
                            'part_time' => 'Part-time',
                            'contract' => 'Contract',
                            'internship' => 'Internship',
                        ]),
                ]),

                RichEditor::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('careers/description-images')
                    ->fileAttachmentsVisibility('public')
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'link'],
                        ['h2', 'h3'],
                        ['bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ]),

                Select::make('status')
                    ->required()
                    ->default('draft')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                    ])
                    ->helperText('Published postings are visible on the public careers page. Set to Closed once the role is filled.'),
            ]);
    }
}
