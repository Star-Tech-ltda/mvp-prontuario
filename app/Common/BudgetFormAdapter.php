<?php

use App\Enums\CostType;
use App\Models\ExpenseCategory;
use App\Models\ProcedureCategory;
use Filament\Forms\Components\{
    Repeater, Select, TextInput, Placeholder, Actions\Action, Hidden
};

function categoryItensRepeater(
    string $categoryField,
    \Closure $categoryOptions,
    string $itemField,
    string $itemLabel,
    \Closure $itemOptions,
    \Closure $itemModel,
    string $modalTitle
): Repeater {
    return Repeater::make("{$categoryField}")
        ->hiddenLabel()
        ->addActionLabel("Adicionar")
        ->collapsible()
        ->columns(5)
        ->defaultItems(1)
        ->schema([
            Select::make($categoryField)
                ->label("Categoria de {$itemLabel}")
                ->required()
                ->reactive()
                ->searchable()
                ->columnSpan(4)
                ->dehydrated(false)
                ->options($categoryOptions)
                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                ->suffixAction(
                    Action::make('view_itens')
                        ->hiddenLabel()
                        ->icon('heroicon-o-eye')
                        ->modalHeading($modalTitle)
                        ->modalSubmitActionLabel('Salvar')
                        ->modalWidth('3xl')
                        ->action(function (array $data, callable $set) use ($itemField) {
                            $set($itemField, $data[$itemField]);
                        })
                        ->form(fn ($get) => [
                            Repeater::make($itemField)
                                ->hiddenLabel()
                                ->addActionLabel("Adicionar")
                                ->columns(2)
                                ->default(fn () => $get($itemField) ?? [ ])
                                ->minItems(1)
                                ->schema([
                                    Select::make("{$itemField}_id")
                                        ->label($itemLabel)
                                        ->required()
                                        ->searchable()
                                        ->reactive()
                                        ->options(fn () => $itemOptions($get($categoryField)))
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                    TextInput::make('quantity')
                                        ->label(fn ($get) => ($model = str_contains($categoryField, 'procedure') ? ProcedureCategory::find($get($categoryField)) : ExpenseCategory::find($get($categoryField))) && $model->cost_type ? CostType::from($model->cost_type)->pluralLabel() : 'Quantidade')
                                        ->numeric()
                                        ->default(1)
                                        ->required()
                                        ->reactive()
                                        ->minValue(0),

                                    Placeholder::make('Valor Unitário: ')
                                        ->inlineLabel()
                                        ->content(function (callable $get) use ($itemModel, $itemField) {
                                            $model = $itemModel($get("{$itemField}_id"));
                                            return 'R$ ' . number_format($model?->default_price ?? 0, 2, ',', '.');
                                        }),

                                    Placeholder::make('Subtotal: ')
                                        ->inlineLabel()
                                        ->content(function (callable $get) use ($itemModel, $itemField) {
                                            $model = $itemModel($get("{$itemField}_id"));
                                            $quantity = $get('quantity') ?? 0;
                                            return 'R$ ' . number_format(($model?->default_price ?? 0) * $quantity, 2, ',', '.');
                                        }),
                                ])
                        ])
                ),

            Placeholder::make("Total")
                ->columnSpan(1)
                ->content(fn (callable $get) => 'R$ ' . number_format(
                    collect($get($itemField) ?? [])
                        ->sum(fn ($item) => ($itemModel($item["{$itemField}_id"])?->default_price ?? 0) * ($item['quantity'] ?? 0)),
                    2, ',', '.'
                )),

            Hidden::make($itemField)
                ->dehydrated()
                ->default([]),
        ]);
}
