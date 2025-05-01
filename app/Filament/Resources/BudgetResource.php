<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Tables;
use App\Models\Budget;
use App\Models\Expense;
use Filament\Forms\Form;
use App\Models\Procedure;
use Filament\Tables\Table;
use App\Models\ExpenseCategory;
use Filament\Resources\Resource;
use App\Models\ProcedureCategory;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use App\Filament\Resources\BudgetResource\Pages;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationLabel = 'Orçamentos';

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Financeiro';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Atividades')
                        ->description('Informe as atividades realizadas')
                        ->schema([
                            categoryItensRepeater(
                                categoryField: 'procedure_category',
                                categoryLabel: 'Categoria do Procedimento',
                                categoryOptions: fn () => ProcedureCategory::pluck('name', 'id'),
                                itemField: 'procedures',
                                itemLabel: 'Procedimento',
                                itemOptions: fn ($categoryId) => Procedure::where('procedure_category_id', $categoryId)->pluck('name', 'id'),
                                itemModel: fn ($id) => Procedure::find($id),
                                modalTitle: 'Procedimentos da Categoria'
                            ),
                        ]),

                    Step::make('Ajustes Financeiros')
                        ->description('Informe as despesas e outras variáveis financeiras')
                        ->schema([
                            TextInput::make('profit_margin'),
                            categoryItensRepeater(
                                categoryField: 'expense_category',
                                categoryLabel: 'Categoria da Despesa',
                                categoryOptions: fn () => ExpenseCategory::pluck('name', 'id'),
                                itemField: 'expenses',
                                itemLabel: 'Despesa',
                                itemOptions: fn ($categoryId) => Expense::where('expense_category_id', $categoryId)->pluck('name', 'id'),
                                itemModel: fn ($id) => Expense::find($id),
                                modalTitle: 'Despesas da Categoria'
                            ),
                        ]),

                    Step::make('Relatório Final')
                        ->description('Visualise os resultados')
                        ->schema([

                        ]),
                ])->columnSpan('full')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
