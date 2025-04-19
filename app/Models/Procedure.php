<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Procedure extends Model
{
    use HasFactory;

    protected $fillable = [
        'procedure_category_id',
        'name',
        'default_price',
        'editable_price'
    ];

    public function procedureCategory(): BelongsTo
    {
        return $this->belongsTo(ProcedureCategory::class);
    }

    public function budgets(): BelongsToMany
    {
        return $this->belongsToMany(Budget::class);
    }
}