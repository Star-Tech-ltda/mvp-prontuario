<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BudgetProcedure extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'budget_id',
        'procedure_id',
        'quantity',
        'price_override',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }
}
