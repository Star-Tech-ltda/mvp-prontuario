<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Models\Expense;
use App\Models\Procedure;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBudget extends EditRecord
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['expense_category'] = $this->record->budgetExpense()
            ->with('expense')
            ->get()
            ->groupBy('expense.expense_category_id')
            ->map(function ($group, $categoryId) {
                return [
                    'expense_category' => $categoryId,
                    'expenses' => $group->map(function ($item) {
                        return [
                            'expenses_id' => $item->expense_id,
                            'quantity' => $item->quantity,
                        ];
                    })->toArray(),
                ];
            })
            ->values()
            ->toArray();

        $data['procedure_category'] = $this->record->budgetProcedure()
            ->with('procedure')
            ->get()
            ->groupBy('procedure.procedure_category_id')
            ->map(function ($group, $categoryId) {
                return [
                    'procedure_category' => $categoryId,
                    'procedures' => $group->map(function ($item) {
                        return [
                            'procedures_id' => $item->procedure_id,
                            'quantity' => $item->quantity,
                        ];
                    })->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return $data;
    }
}
