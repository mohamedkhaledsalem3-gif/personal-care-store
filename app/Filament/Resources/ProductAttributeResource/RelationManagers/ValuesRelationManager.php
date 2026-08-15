<?php

namespace App\Filament\Resources\ProductAttributeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'values';

    protected static ?string $title = 'قيم الخاصية';

    protected static ?string $modelLabel = 'قيمة';

    protected static ?string $pluralModelLabel = 'قيم الخاصية';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('value')
                ->label('القيمة')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255),

            Forms\Components\ColorPicker::make('color_code')
                ->label('اللون'),

            Forms\Components\Toggle::make('is_active')
                ->label('نشطة')
                ->default(true),

            Forms\Components\TextInput::make('sort_order')
                ->label('ترتيب العرض')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->columns([
                Tables\Columns\TextColumn::make('value')
                    ->label('القيمة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug'),

                Tables\Columns\ColorColumn::make('color_code')
                    ->label('اللون'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشطة')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة قيمة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}