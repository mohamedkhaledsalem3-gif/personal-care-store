<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'خيارات المنتج';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('اسم الخيار')
                ->required()
                ->maxLength(255)
                ->placeholder('مثال: 400 ml'),

            Forms\Components\TextInput::make('sku')
                ->label('SKU')
                ->required()
                ->maxLength(100)
                ->unique(
                    table: 'product_variants',
                    column: 'sku',
                    ignoreRecord: true,
                ),

            Forms\Components\Grid::make(3)
                ->schema([
                    Forms\Components\TextInput::make('cost_price')
                        ->label('سعر التكلفة')
                        ->numeric()
                        ->minValue(0)
                        ->required(),

                    Forms\Components\TextInput::make('price')
                        ->label('السعر')
                        ->numeric()
                        ->minValue(0)
                        ->required(),

                    Forms\Components\TextInput::make('sale_price')
                        ->label('سعر التخفيض')
                        ->numeric()
                        ->minValue(0),
                ]),

            Forms\Components\Grid::make(3)
                ->schema([
                    Forms\Components\TextInput::make('stock_quantity')
                        ->label('المخزون')
                        ->numeric()
                        ->integer()
                        ->minValue(0)
                        ->required(),

                    Forms\Components\TextInput::make('low_stock_threshold')
                        ->label('حد المخزون المنخفض')
                        ->numeric()
                        ->integer()
                        ->minValue(0)
                        ->default(5)
                        ->required(),

                    Forms\Components\TextInput::make('unit')
                        ->label('الوحدة')
                        ->maxLength(50)
                        ->placeholder('ml / g / قطعة'),
                ]),

            Forms\Components\TextInput::make('weight')
                ->label('الوزن بالكيلو')
                ->numeric()
                ->minValue(0),

            Forms\Components\Toggle::make('is_default')
                ->label('الخيار الافتراضي')
                ->default(false),

            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')

            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الخيار')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('EGP'),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('التخفيض')
                    ->money('EGP'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('المخزون')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('افتراضي')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])

            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة خيار'),
            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['is_default'] ?? false) === true) {
            $this->getOwnerRecord()
                ->variants()
                ->update([
                    'is_default' => false,
                ]);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['is_default'] ?? false) === true) {
            $record = $this->getMountedTableActionRecord();

            $this->getOwnerRecord()
                ->variants()
                ->when(
                    $record,
                    fn ($query) => $query->whereKeyNot($record->getKey())
                )
                ->update([
                    'is_default' => false,
                ]);
        }

        return $data;
    }
}