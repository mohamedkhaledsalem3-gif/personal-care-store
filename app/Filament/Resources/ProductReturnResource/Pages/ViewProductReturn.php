<?php

namespace App\Filament\Resources\ProductReturnResource\Pages;

use App\Enums\ReturnStatus;
use App\Filament\Resources\ProductReturnResource;
use App\Models\ProductReturn;
use App\Services\ReturnService;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProductReturn extends ViewRecord
{
    protected static string $resource = ProductReturnResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Return Information')
                    ->schema([
                        TextEntry::make('return_number')
                            ->label('Return Number'),

                        TextEntry::make('order.order_number')
                            ->label('Order'),

                        TextEntry::make('user.name')
                            ->label('Customer'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(
                                fn ($state) => $state instanceof ReturnStatus
                                    ? $state->label()
                                    : ReturnStatus::tryFrom((string) $state)?->label()
                                        ?? (string) $state
                            )
                            ->color(
                                fn ($state) => $state instanceof ReturnStatus
                                    ? $state->color()
                                    : ReturnStatus::tryFrom((string) $state)?->color()
                                        ?? 'gray'
                            ),

                        TextEntry::make('requested_at')
                            ->label('Requested At')
                            ->dateTime('Y-m-d H:i:s'),

                        TextEntry::make('approved_at')
                            ->label('Approved At')
                            ->dateTime('Y-m-d H:i:s')
                            ->placeholder('-'),

                        TextEntry::make('received_at')
                            ->label('Received At')
                            ->dateTime('Y-m-d H:i:s')
                            ->placeholder('-'),

                        TextEntry::make('completed_at')
                            ->label('Completed At')
                            ->dateTime('Y-m-d H:i:s')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Return Details')
                    ->schema([
                        TextEntry::make('reason')
                            ->label('Reason'),

                        TextEntry::make('customer_note')
                            ->label('Customer Note')
                            ->placeholder('-'),

                        TextEntry::make('admin_note')
                            ->label('Admin Note')
                            ->placeholder('-'),
                    ])
                    ->columns(1),

                Section::make('Return Items')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('orderItem.product_name')
                                    ->label('Product'),

                                TextEntry::make('orderItem.sku')
                                    ->label('SKU'),

                                TextEntry::make('variant.name')
                                    ->label('Variant'),

                                TextEntry::make('quantity')
                                    ->label('Quantity'),

                                TextEntry::make('reason')
                                    ->label('Reason'),

                                TextEntry::make('condition')
                                    ->label('Condition'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(
                    fn (ProductReturn $record): bool =>
                        $record->status === ReturnStatus::PENDING
                )
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        app(ReturnService::class)->approve($this->record);

                        Notification::make()
                            ->title('Return approved')
                            ->success()
                            ->send();

                        $this->refreshFormData([
                            'status',
                            'approved_at',
                        ]);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Unable to approve Return')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(
                    fn (ProductReturn $record): bool =>
                        $record->status === ReturnStatus::PENDING
                )
                ->form([
                    \Filament\Forms\Components\Textarea::make('admin_note')
                        ->label('Admin Note')
                        ->required()
                        ->rows(4),
                ])
                ->requiresConfirmation()
                ->action(function (array $data): void {
                    try {
                        app(ReturnService::class)->reject(
                            $this->record,
                            $data['admin_note']
                        );

                        Notification::make()
                            ->title('Return rejected')
                            ->success()
                            ->send();

                        $this->refreshFormData([
                            'status',
                            'rejected_at',
                            'admin_note',
                        ]);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Unable to reject Return')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('receive')
                ->label('Receive')
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('info')
                ->visible(
                    fn (ProductReturn $record): bool =>
                        $record->status === ReturnStatus::APPROVED
                )
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        app(ReturnService::class)->receive($this->record);

                        Notification::make()
                            ->title('Return received')
                            ->success()
                            ->send();

                        $this->refreshFormData([
                            'status',
                            'received_at',
                        ]);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Unable to receive Return')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('complete')
                ->label('Complete')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(
                    fn (ProductReturn $record): bool =>
                        $record->status === ReturnStatus::RECEIVED
                )
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        app(ReturnService::class)->complete($this->record);

                        Notification::make()
                            ->title('Return completed')
                            ->success()
                            ->send();

                        $this->refreshFormData([
                            'status',
                            'completed_at',
                        ]);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Unable to complete Return')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url(
                    fn (): string =>
                        ProductReturnResource::getUrl('index')
                ),
        ];
    }
}