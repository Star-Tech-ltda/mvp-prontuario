<?php

namespace App\Models;

use App\Enums\CostType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'cost_type',
      'description',
    ];

    protected $casts = [
        'cost_type'=>CostType::class,
    ];

    public function expenses(): hasMany
    {
        return $this->hasMany(Expense::class);
    }
}