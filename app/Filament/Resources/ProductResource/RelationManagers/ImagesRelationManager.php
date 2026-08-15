<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'صور المنتج';

    protected static ?string $modelLabel = 'صورة';

    protected static ?string $pluralModelLabel = 'صور المنتج';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\FileUpload::make('image_path')
                    ->label('صورة المنتج')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->imageEditor()
                    ->required(),

                Forms\Components\TextInput::make('alt_text')
                    ->label('النص البديل')
                    ->placeholder('وصف الصورة لمحركات البحث وإمكانية الوصول')
                    ->maxLength(255),

                Forms\Components\TextInput::make('sort_order')
                    ->label('ترتيب الصورة')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                Forms\Components\Toggle::make('is_primary')
                    ->label('الصورة الرئيسية')
                    ->default(false),

                Forms\Components\Toggle::make('is_active')
                    ->label('نشطة')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('image_path')

            ->columns([

                Tables\Columns\ImageColumn::make('image_path')
                    ->label('الصورة')
                    ->disk('public')
                    ->square(),

                Tables\Columns\TextColumn::make('alt_text')
                    ->label('النص البديل')
                    ->limit(40)
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_primary')
                    ->label('رئيسية')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشطة')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d H:i'),
            ])

            ->defaultSort('sort_order')

            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة صورة'),
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
}