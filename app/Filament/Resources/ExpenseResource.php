<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ExpenseCluster;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $cluster = ExpenseCluster::class;

    protected static SubNavigationPosition $subNavigationPosition = subNavigationPosition::Top;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Tipos de Despesa';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return 'Tipo de Despesa';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('expense_category_id')
                    ->relationship('expenseCategory', 'name')
                    ->label('Qual a categoria desta Despesa?')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nome')
                    ->maxLength(255),
                Forms\Components\TextInput::make('default_price')
                    ->label('Valor Padrão')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01),
                Forms\Components\Toggle::make('editable_price')
                    ->label('Valor Editável')
                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expense_category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('editable_price')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageExpense::route('/'),
        ];
    }
}
