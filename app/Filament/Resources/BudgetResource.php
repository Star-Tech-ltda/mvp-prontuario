<?php

namespace App\Filament\Resources;

use App\Common\BudgetCalculations;
use App\Filament\Resources\BudgetResource\Pages\CreateBudget;
use App\Filament\Resources\BudgetResource\Pages\EditBudget;
use App\Filament\Resources\BudgetResource\Pages\ListBudgets;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Budget;
use App\Models\Expense;
use Filament\Forms\Form;
use App\Models\Procedure;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\ExpenseCategory;
use Filament\Resources\Resource;
use App\Models\ProcedureCategory;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationLabel = 'Orçamentos';

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Financeiro';

    public static function getModelLabel(): string
    {
        return 'Orçamento';
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Métricas Financeiras')
                        ->description('Informe as atividades e despesas')
                        ->schema([
                            Select::make('hourly_rate')
                                ->label('Taxa de Horário')
                                ->required()
                                ->prefixIcon('heroicon-o-clock')
                                ->relationship('hourlyRate', 'name'),
                            TextInput::make('profit_margin')
                                ->label('Lucro Esperado')
                                ->required()
                                ->placeholder('0,00')
                                ->suffix('%'),
                            categoryItensRepeater(
                                categoryField: 'procedure_category',
                                categoryOptions: fn () => ProcedureCategory::pluck('name', 'id'),
                                itemField: 'procedures',
                                itemLabel: 'Procedimento',
                                itemOptions: fn ($categoryId) => Procedure::where('procedure_category_id', $categoryId)->pluck('name', 'id'),
                                itemModel: fn ($id) => Procedure::find($id),
                                modalTitle: 'Procedimentos da Categoria'
                            )
                            ->columnSpan(1),
                            categoryItensRepeater(
                                categoryField: 'expense_category',
                                categoryOptions: fn () => ExpenseCategory::pluck('name', 'id'),
                                itemField: 'expenses',
                                itemLabel: 'Despesa',
                                itemOptions: fn ($categoryId) => Expense::where('expense_category_id', $categoryId)->pluck('name', 'id'),
                                itemModel: fn ($id) => Expense::find($id),
                                modalTitle: 'Despesas da Categoria'
                            )
                            ->columnSpan(1),
                        ])
                    ->columns(2),

                    Step::make('Relatório')
                    ->description('Visualize os resultados')
                    ->schema([
                        Group::make([
                            Select::make('payment_method')
                                ->label('Método de Pagamento')
                                ->required()
                                ->prefixIcon('heroicon-o-credit-card')
                                ->columnSpan(2)
                                ->relationship('paymentMethod', 'name')
                                ->reactive()
                                ->afterStateUpdated(function (callable $set, callable $get) {
                                    $set('base_price', BudgetCalculations::calculateBasePrice($get));
                                    $set('cost_price', BudgetCalculations::calculateCostPrice($get));
                                    $set('price_with_profit', BudgetCalculations::calculatePriceWithProfitMargin($get));
                                    $set('sale_price', BudgetCalculations::calculateSalePrice($get));
                                }),
                            Grid::make()
                                ->schema([
                                    Placeholder::make('')
                                        ->label('Valor Base dos Procedimentos')
                                        ->columnSpan(3),
                                    Placeholder::make('base_price_display')
                                        ->label(false)
                                        ->content(fn ($get) =>
                                            'R$ ' . number_format(BudgetCalculations::calculateBasePrice($get), 2, ',', '.')
                                        ),
                                    Hidden::make('base_price'),

                                    Placeholder::make('')
                                        ->label('Valor com Custos de Despesas')
                                        ->columnSpan(3),
                                    Placeholder::make('cost_price_display')
                                        ->label(false)
                                        ->content(fn ($get) =>
                                            'R$ ' . number_format(BudgetCalculations::calculateCostPrice($get), 2, ',', '.')
                                        ),
                                    Hidden::make('cost_price'),

                                    Placeholder::make('')
                                        ->label('Preço Total com Lucro')
                                        ->columnSpan(3),
                                    Placeholder::make('price_with_profit_display')
                                        ->label(false)
                                        ->content(fn ($get) =>
                                            'R$ ' . number_format(BudgetCalculations::calculatePriceWithProfitMargin($get), 2, ',', '.')
                                        ),
                                    Hidden::make('price_with_profit'),

                                    Placeholder::make('')
                                        ->label('Preço Total de Venda')
                                        ->columnSpan(3),
                                    Placeholder::make('sale_price_display')
                                        ->label(false)
                                        ->content(fn ($get) =>
                                            'R$ ' . number_format(BudgetCalculations::calculateSalePrice($get), 2, ',', '.')
                                        ),
                                    Hidden::make('sale_price'),
                                ])
                                ->columns(4)
                                ->columnSpanFull()
                        ])
                        ->extraAttributes([
                            'class' => 'mx-auto w-full max-w-md'
                        ]),
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Criado Por')
                    ->searchable(),
                TextColumn::make('paymentMethod.name')
                    ->label('Método de Pagamento'),
                TextColumn::make('hourlyRate.adjustment_percent')
                    ->label('Taxa Horária'),
                TextColumn::make('profit_margin')
                    ->label('Margem de Lucro'),
                TextColumn::make('base_price')
                    ->label('Valor Base dos Procedimentos'),
                TextColumn::make('cost_price')
                    ->label('Valor com Custos de Despesas'),
                TextColumn::make('price_with_profit')
                    ->label('Valor com Lucro'),
                TextColumn::make('sale_price')
                    ->label('Valor de Venda'),
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
            'index' => ListBudgets::route('/'),
            'create' => CreateBudget::route('/create'),
            'edit' => EditBudget::route('/{record}/edit'),
        ];
    }
}
