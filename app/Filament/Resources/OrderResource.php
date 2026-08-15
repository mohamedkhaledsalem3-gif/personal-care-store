<?php


namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\RefundsRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\ReturnsRelationManager;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'الطلبات';

    protected static ?string $modelLabel = 'طلب';

    protected static ?string $pluralModelLabel = 'الطلبات';

    protected static ?string $navigationGroup = 'المبيعات';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الطلب')
                    ->icon('heroicon-o-shopping-cart')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('رقم الطلب')
                            ->disabled(),

                        Forms\Components\TextInput::make('customer_name')
                            ->label('اسم العميل')
                            ->disabled(),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label('هاتف العميل')
                            ->disabled(),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('طريقة الدفع')
                            ->disabled(),

                        Forms\Components\TextInput::make('payment_status')
                            ->label('حالة الدفع')
                            ->disabled(),

                        Forms\Components\TextInput::make('status')
                            ->label('حالة الطلب')
                            ->disabled(),

                        Forms\Components\Textarea::make('shipping_address')
                            ->label('عنوان الشحن')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(),

                        Forms\Components\TextInput::make('shipping_city')
                            ->label('المحافظة')
                            ->disabled(),

                        Forms\Components\TextInput::make('shipping_area')
                            ->label('المنطقة')
                            ->disabled(),

                        Forms\Components\TextInput::make('shipping_postal_code')
                            ->label('الرمز البريدي')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('المبالغ')
                    ->icon('heroicon-o-banknotes')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('الإجمالي الفرعي')
                            ->numeric()
                            ->prefix('EGP')
                            ->disabled(),

                        Forms\Components\TextInput::make('shipping_fee')
                            ->label('الشحن')
                            ->numeric()
                            ->prefix('EGP')
                            ->disabled(),

                        Forms\Components\TextInput::make('discount')
                            ->label('الخصم')
                            ->numeric()
                            ->prefix('EGP')
                            ->disabled(),

                        Forms\Components\TextInput::make('total')
                            ->label('الإجمالي النهائي')
                            ->numeric()
                            ->prefix('EGP')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('ملاحظات')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Forms\Components\Textarea::make('customer_note')
                            ->label('ملاحظة العميل')
                            ->rows(4)
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('العميل')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('الإجمالي')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'cod' => 'الدفع عند الاستلام',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'refunded' => 'info',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'معلق',
                        'paid' => 'مدفوع',
                        'refunded' => 'مسترد',
                        'failed' => 'فشل',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('حالة الطلب')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'processing' => 'primary',
                        'shipped' => 'info',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'جديد',
                        'confirmed' => 'مؤكد',
                        'processing' => 'قيد التجهيز',
                        'shipped' => 'تم الشحن',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغي',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime()
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة الطلب')
                    ->options([
                        'pending' => 'جديد',
                        'confirmed' => 'مؤكد',
                        'processing' => 'قيد التجهيز',
                        'shipped' => 'تم الشحن',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغي',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('حالة الدفع')
                    ->options([
                        'pending' => 'معلق',
                        'paid' => 'مدفوع',
                        'refunded' => 'مسترد',
                        'failed' => 'فشل',
                    ]),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        app(OrderService::class)->confirm($record);
                    }),

                Tables\Actions\Action::make('process')
                    ->label('Process')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('primary')
                    ->visible(fn (Order $record) => $record->status === 'confirmed')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        app(OrderService::class)->process($record);
                    }),

                Tables\Actions\Action::make('ship')
                    ->label('Ship')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->visible(fn (Order $record) => $record->status === 'processing')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        app(OrderService::class)->ship($record);
                    }),

                Tables\Actions\Action::make('deliver')
                    ->label('Deliver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === 'shipped')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        app(OrderService::class)->deliver($record);
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Order $record) => in_array(
                        $record->status,
                        [
                            'pending',
                            'confirmed',
                            'processing',
                        ],
                        true
                    ))
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('سبب الإلغاء')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (Order $record, array $data) {
                        app(OrderService::class)->cancel(
                            $record,
                            $data['reason']
                        );
                    }),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ReturnsRelationManager::class,
            RefundsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'user',
                'items',
                'returns',
                'refunds',
            ]);
    }
}
