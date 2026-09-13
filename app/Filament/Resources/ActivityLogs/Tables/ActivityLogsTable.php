<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Models\ActivityLog;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('System')
                    ->sortable(),
                TextColumn::make('action')
                    ->badge()
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                    ])
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Content type')
                    ->formatStateUsing(fn (string $state): string => Str::headline(class_basename($state)))
                    ->sortable(),
                TextColumn::make('subject_label')
                    ->label('Item')
                    ->placeholder('—')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->options(array_combine(ActivityLog::ACTIONS, array_map('ucfirst', ActivityLog::ACTIONS))),
                SelectFilter::make('user_id')
                    ->label('User')
                    ->options(fn () => User::query()->pluck('name', 'id')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->schema([
                        Placeholder::make('subject_type')
                            ->label('Content type')
                            ->content(fn (ActivityLog $record): string => Str::headline(class_basename($record->subject_type))),
                        Placeholder::make('subject_label')
                            ->label('Item')
                            ->content(fn (ActivityLog $record): string => $record->subject_label ?? '—'),
                        KeyValue::make('changes')
                            ->label('Details')
                            ->disabled(),
                    ]),
            ]);
    }
}
