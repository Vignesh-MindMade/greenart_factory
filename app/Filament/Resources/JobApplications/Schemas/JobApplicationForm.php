<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Applicant')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('job_posting_id')
                                ->relationship('jobPosting', 'title')
                                ->required()
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('name')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('email')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('phone')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                        Textarea::make('cover_note')
                            ->label('Cover note')
                            ->rows(4)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Select::make('status')
                    ->required()
                    ->options([
                        'new' => 'New',
                        'shortlisted' => 'Shortlisted',
                        'rejected' => 'Rejected',
                    ]),
            ]);
    }
}
