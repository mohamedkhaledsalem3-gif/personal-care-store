<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductAttributeResource\Pages;
use App\Filament\Resources\ProductAttributeResource\RelationManagers;
use App\Models\ProductAttribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductAttributeResource extends Resource
{
    protected static ?string $model = ProductAttribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'خصائص المنتجات';

    protected static ?string $modelLabel = 'خاصية';

    protected static ?string $pluralModelLabel = 'خصائص المنتجات';

    protected static ?string $navigationGroup = 'الكتالوج';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الخاصية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الخاصية')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        Forms\Components\TextInput::make('slug')
                            ->label('الرابط المختصر')
                            ->required()
                            ->unique(
                                ignoreRecord: true
                            )
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label('نوع الخاصية')
                            ->options([
                                'select' => 'اختيار واحد',
                                'color' => 'لون',
                                'text' => 'نص',
                            ])
                            ->default('select')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_filterable')
                            ->label('تظهر في الفلاتر')
                            ->default(true),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشطة')
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتيب العرض')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الخاصية')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'select' => 'اختيار',
                        'color' => 'لون',
                        'text' => 'نص',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('values_count')
                    ->label('عدد القيم')
                    ->counts('values'),

                Tables\Columns\IconColumn::make('is_filterable')
                    ->label('فلترة')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشطة')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
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
            RelationManagers\ValuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductAttributes::route('/'),
            'create' => Pages\CreateProductAttribute::route('/create'),
            'edit' => Pages\EditProductAttribute::route('/{record}/edit'),
        ];
    }
}