<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'time_context_id',
        'payment_method_id',
        'profit_margin',
        'base_price',
        'cost_price',
        'price_with_profit',
        'sale_price'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function timeContext(): BelongsTo
    {
        return $this->belongsTo(HourlyRate::class);
    }

    public function budgetExpense(): HasMany
    {
        return $this->hasMany(BudgetExpense::class);
    }

    public function budgetProcedure(): HasMany
    {
        return $this->hasMany(BudgetProcedure::class);
    }
}
