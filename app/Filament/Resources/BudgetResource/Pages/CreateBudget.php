<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Procedure;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['user_id'] = Auth::id();

        $budget = Budget::create($data);

        foreach ($data['procedure_category'] ?? [] as $categoryData) {
            foreach ($categoryData['procedures'] ?? [] as $procedureData) {
                $procedure = Procedure::find($procedureData['procedures_id']);

                if ($procedure) {
                    $budget->budgetProcedure()->create([
                        'procedure_id' => $procedure->id,
                        'quantity' => $procedureData['quantity'] ?? 1,
                        'price_override' => $procedure->default_price * ($procedureData['quantity'] ?? 1),
                    ]);
                }
            }
        }

        foreach ($data['expense_category'] ?? [] as $categoryData) {
            foreach ($categoryData['expenses'] ?? [] as $expenseData) {
                $expense = Expense::find($expenseData['expenses_id']);

                if ($expense) {
                    $budget->budgetExpense()->create([
                        'expense_id' => $expense->id,
                        'quantity' => $expenseData['quantity'] ?? 1,
                        'price_override' => $expense->default_price * ($expenseData['quantity'] ?? 1),
                    ]);
                }
            }
        }

        return $budget;
    }
}
