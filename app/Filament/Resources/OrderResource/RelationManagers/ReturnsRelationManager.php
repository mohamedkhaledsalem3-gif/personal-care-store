<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\ProductReturn;
use App\Services\ReturnService;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ReturnsRelationManager extends RelationManager
{
    protected static string $relationship = 'returns';

    protected static ?string $title = 'Returns';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('return_number')
                    ->label('Return Number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof \BackedEnum
                        ? $state->value
                        : $state)
                    ->color(fn ($state) => match (
                        $state instanceof \BackedEnum ? $state->value : $state
                    ) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'received' => 'primary',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(40),

                Tables\Columns\TextColumn::make('requested_at')
                    ->dateTime()
                    ->sortable(),
            ])

            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(ReturnService::class)->approve($record);
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(ReturnService::class)->reject(
                            $record,
                            'تم رفض طلب الإرجاع من الإدارة.'
                        );
                    }),

                Tables\Actions\Action::make('receive')
                    ->label('Receive')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(ReturnService::class)->receive($record);
                    }),

                Tables\Actions\Action::make('complete')
                    ->label('Complete')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'received')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(ReturnService::class)->complete($record);
                    }),
            ]);
    }
}