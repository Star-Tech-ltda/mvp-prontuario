<?php

use Filament\Forms\Components\{
    Repeater, Select, TextInput, Placeholder, Actions\Action, Hidden
};

function categoryItensRepeater(
    string $categoryField,
    string $categoryLabel,
    \Closure $categoryOptions,
    string $itemField,
    string $itemLabel,
    \Closure $itemOptions,
    \Closure $itemModel,
    string $modalTitle
): Repeater {
    return Repeater::make("{$itemField}_category")
        ->schema([
            Select::make($categoryField)
                ->label($categoryLabel)
                ->options($categoryOptions)
                ->required()
                ->dehydrated(false)
                ->reactive()
                ->suffixAction(
                    Action::make('view_itens')
                        ->hiddenLabel()
                        ->icon('heroicon-o-eye')
                        ->modalHeading($modalTitle)
                        ->modalSubmitActionLabel('Salvar')
                        ->form(fn ($get) => [
                            Repeater::make($itemField)
                                ->label($itemLabel)
                                ->schema([
                                    Select::make("{$itemField}_id")
                                        ->label($itemLabel)
                                        ->options(fn () => $itemOptions($get($categoryField)))
                                        ->required()
                                        ->searchable()
                                        ->columnSpan(2)
                                        ->reactive(),

                                    TextInput::make('quantity')
                                        ->label('Quantidade')
                                        ->numeric()
                                        ->default(1)
                                        ->required()
                                        ->reactive()
                                        ->minValue(0),

                                    Placeholder::make('Valor Unitário')
                                        ->content(function (callable $get) use ($itemModel, $itemField) {
                                            $model = $itemModel($get("{$itemField}_id"));
                                            return 'R$ ' . number_format($model?->default_price ?? 0, 2, ',', '.');
                                        }),

                                    Placeholder::make('Subtotal')
                                        ->content(function (callable $get) use ($itemModel, $itemField) {
                                            $model = $itemModel($get("{$itemField}_id"));
                                            $quantity = $get('quantity') ?? 0;
                                            return 'R$ ' . number_format(($model?->default_price ?? 0) * $quantity, 2, ',', '.');
                                        }),
                                ])
                                ->columns(5)
                                ->default(fn () => $get($itemField) ?? []),
                        ])
                        ->action(function (array $data, callable $set) use ($itemField) {
                            $set($itemField, $data[$itemField]);
                        })
                ),

            Placeholder::make("total_{$itemField}")
                ->content(fn (callable $get) => 'R$ ' . number_format(
                    collect($get($itemField) ?? [])
                        ->sum(fn ($item) => ($itemModel($item["{$itemField}_id"])?->default_price ?? 0) * ($item['quantity'] ?? 0)),
                    2, ',', '.'
                )),

            Hidden::make($itemField)
                ->dehydrated(true)
                ->default([]),
        ])
        ->columns(2)
        ->defaultItems(1);
}