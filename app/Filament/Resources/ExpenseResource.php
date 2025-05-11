<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ExpenseCluster;
use App\Filament\Resources\ExpenseResource\Pages\ManageExpense;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Leandrocfe\FilamentPtbrFormFields\Money;

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
                Select::make('expense_category_id')
                    ->relationship('expenseCategory', 'name')
                    ->label('Qual a categoria desta Despesa?')
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->label('Nome')
                    ->maxLength(255),
                TextInput::make('default_price')
                    ->label('Valor Padrão')
                    ->required()
                    ->numeric()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters('.')
                    ->default('0,00'),
                Toggle::make('editable_price')
                    ->label('Valor Editável'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense_category_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('default_price')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('editable_price')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ManageExpense::route('/'),
        ];
    }
}
