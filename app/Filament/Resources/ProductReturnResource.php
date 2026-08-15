<?php

namespace App\Filament\Resources;

use App\Enums\ReturnStatus;
use App\Filament\Resources\ProductReturnResource\Pages;
use App\Models\ProductReturn;
use App\Services\ReturnService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductReturnResource extends Resource
{
    protected static ?string $model = ProductReturn::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $navigationLabel = 'Returns';

    protected static ?string $modelLabel = 'Return';

    protected static ?string $pluralModelLabel = 'Returns';

    protected static ?string $navigationGroup = 'Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('return_number')
                    ->label('Return Number')
                    ->disabled(),

                Forms\Components\TextInput::make('status')
                    ->label('Status')
                    ->disabled(),

                Forms\Components\Textarea::make('reason')
                    ->label('Reason')
                    ->disabled(),

                Forms\Components\Textarea::make('customer_note')
                    ->label('Customer Note')
                    ->disabled(),

                Forms\Components\Textarea::make('admin_note')
                    ->label('Admin Note')
                    ->rows(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([
                Tables\Columns\TextColumn::make('return_number')
                    ->label('Return Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) => $state instanceof ReturnStatus
                            ? $state->label()
                            : ReturnStatus::tryFrom((string) $state)?->label() ?? (string) $state
                    )
                    ->color(
                        fn ($state) => $state instanceof ReturnStatus
                            ? $state->color()
                            : ReturnStatus::tryFrom((string) $state)?->color() ?? 'gray'
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->reason),

                Tables\Columns\TextColumn::make('requested_at')
                    ->label('Requested At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completed At')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(
                        collect(ReturnStatus::cases())
                            ->mapWithKeys(
                                fn (ReturnStatus $status) => [
                                    $status->value => $status->label(),
                                ]
                            )
                            ->toArray()
                    ),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(
                        fn (ProductReturn $record): bool =>
                            $record->status === ReturnStatus::PENDING
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Approve Return')
                    ->modalDescription(
                        'هل أنت متأكد من الموافقة على طلب الإرجاع؟'
                    )
                    ->action(function (ProductReturn $record): void {
                        try {
                            app(ReturnService::class)->approve($record);

                            Notification::make()
                                ->title('Return approved')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Unable to approve Return')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(
                        fn (ProductReturn $record): bool =>
                            $record->status === ReturnStatus::PENDING
                    )
                    ->form([
                        Forms\Components\Textarea::make('admin_note')
                            ->label('Admin Note')
                            ->required()
                            ->rows(4),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Reject Return')
                    ->modalDescription(
                        'هل أنت متأكد من رفض طلب الإرجاع؟'
                    )
                    ->action(function (
                        ProductReturn $record,
                        array $data
                    ): void {
                        try {
                            app(ReturnService::class)->reject(
                                $record,
                                $data['admin_note']
                            );

                            Notification::make()
                                ->title('Return rejected')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Unable to reject Return')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('receive')
                    ->label('Receive')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->color('info')
                    ->visible(
                        fn (ProductReturn $record): bool =>
                            $record->status === ReturnStatus::APPROVED
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Receive Return')
                    ->modalDescription(
                        'تأكيد استلام المنتجات المرتجعة.'
                    )
                    ->action(function (ProductReturn $record): void {
                        try {
                            app(ReturnService::class)->receive($record);

                            Notification::make()
                                ->title('Return received')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Unable to receive Return')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn (ProductReturn $record): bool =>
                            $record->status === ReturnStatus::RECEIVED
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Complete Return')
                    ->modalDescription(
                        'تأكيد اكتمال دورة الإرجاع.'
                    )
                    ->action(function (ProductReturn $record): void {
                        try {
                            app(ReturnService::class)->complete($record);

                            Notification::make()
                                ->title('Return completed')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Unable to complete Return')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])

            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductReturns::route('/'),
            'create' => Pages\CreateProductReturn::route('/create'),
            'view' => Pages\ViewProductReturn::route('/{record}'),
            'edit' => Pages\EditProductReturn::route('/{record}/edit'),
        ];
    }
}