<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\VariantsRelationManager;

use App\Models\Product;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'المنتجات';

    protected static ?string $modelLabel = 'منتج';

    protected static ?string $pluralModelLabel = 'المنتجات';

    protected static ?string $navigationGroup = 'الكتالوج';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                /*
                |--------------------------------------------------------------------------
                | معلومات المنتج
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('معلومات المنتج')
                    ->description('المعلومات الأساسية للمنتج')
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->label('اسم المنتج')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        Forms\Components\Select::make('category_id')
                            ->label('التصنيف')
                            ->relationship(
                                'category',
                                'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('brand_id')
                            ->label('العلامة التجارية')
                            ->relationship(
                                'brand',
                                'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('slug')
                            ->label('الرابط المختصر Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('unit')
                            ->label('الوحدة / الحجم')
                            ->placeholder('مثال: 400 ml')
                            ->maxLength(100),

                        Forms\Components\Textarea::make('short_description')
                            ->label('الوصف المختصر')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description')
                            ->label('الوصف الكامل')
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | التسعير
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('التسعير')
                    ->description('تكلفة المنتج وأسعاره')
                    ->schema([

                        Forms\Components\TextInput::make('cost_price')
                            ->label('سعر التكلفة')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->prefix('ج.م'),

                        Forms\Components\TextInput::make('price')
                            ->label('السعر الأساسي')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->prefix('ج.م'),

                        Forms\Components\TextInput::make('sale_price')
                            ->label('سعر البيع')
                            ->numeric()
                            ->minValue(0)
                            ->nullable()
                            ->prefix('ج.م'),

                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | المخزون
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('المخزون')
                    ->description('إدارة كمية المنتج والمخزون المنخفض')
                    ->schema([

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('كمية المخزون')
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

                        Forms\Components\TextInput::make('weight')
                            ->label('الوزن بالكيلو')
                            ->numeric()
                            ->minValue(0)
                            ->nullable(),

                        Forms\Components\Select::make('status')
                            ->label('حالة المنتج')
                            ->options([
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                                'draft' => 'مسودة',
                            ])
                            ->default('active')
                            ->required(),

                    ])
                    ->columns(4)
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | التسويق
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('التسويق')
                    ->schema([

                        Forms\Components\Toggle::make('is_featured')
                            ->label('منتج مميز')
                            ->default(false),

                        Forms\Components\Toggle::make('is_new')
                            ->label('منتج جديد')
                            ->default(false),

                        Forms\Components\Toggle::make('is_best_seller')
                            ->label('الأكثر مبيعًا')
                            ->default(false),

                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | SEO
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('تحسين محركات البحث SEO')
                    ->schema([

                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                    ])
                    ->columns(1)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->label('المنتج')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('العلامة التجارية')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('سعر البيع')
                    ->money('EGP')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('المخزون')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'draft',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'draft' => 'مسودة',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('مميز')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_new')
                    ->label('جديد')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_best_seller')
                    ->label('الأكثر مبيعًا')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

            ])
            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name'),

                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('العلامة التجارية')
                    ->relationship('brand', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'draft' => 'مسودة',
                    ]),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

   public static function getRelations(): array
{
    return [
        ImagesRelationManager::class,
        VariantsRelationManager::class,
    ];
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}