<?php

namespace App\Filament\Resources\JobApplications\Tables;

use App\Models\JobApplication;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('jobPosting.title')
                    ->label('Job Posting')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'shortlisted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('job_posting_id')
                    ->label('Job Posting')
                    ->relationship('jobPosting', 'title'),
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'shortlisted' => 'Shortlisted',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download CV')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->visible(fn (JobApplication $record): bool => $record->getFirstMedia('resume') !== null)
                    ->action(function (JobApplication $record) {
                        $media = $record->getFirstMedia('resume');

                        return response()->download($media->getPath(), $media->file_name);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
