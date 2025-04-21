<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BudgetExpense extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'budget_id',
        'expense_id',
        'quantity',
        'price_override',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
