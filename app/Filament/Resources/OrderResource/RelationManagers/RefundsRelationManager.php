<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Services\RefundService;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;

class RefundsRelationManager extends RelationManager
{
    protected static string $relationship = 'refunds';

    protected static ?string $title = 'Refunds';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Refund ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('return.return_number')
                    ->label('Return'),

                Tables\Columns\TextColumn::make('amount')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) => $state instanceof \BackedEnum
                            ? $state->value
                            : $state
                    )
                    ->color(
                        fn ($state) => match (
                            $state instanceof \BackedEnum
                                ? $state->value
                                : $state
                        ) {
                            'pending' => 'warning',
                            'processing' => 'info',
                            'completed' => 'success',
                            'failed' => 'danger',
                            default => 'gray',
                        }
                    ),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])

            ->actions([
                Action::make('process')
                    ->label('Process')
                    ->color('info')
                    ->visible(
                        fn ($record) =>
                            ($record->status instanceof \BackedEnum
                                ? $record->status->value
                                : $record->status) === 'pending'
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(RefundService::class)->process($record);
                    }),

                Action::make('complete')
                    ->label('Complete')
                    ->color('success')
                    ->visible(
                        fn ($record) =>
                            ($record->status instanceof \BackedEnum
                                ? $record->status->value
                                : $record->status) === 'processing'
                    )
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        app(RefundService::class)->complete(
                            $record,
                            $data['transaction_id']
                        );
                    }),
            ]);
    }
}