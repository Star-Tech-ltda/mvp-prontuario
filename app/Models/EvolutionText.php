<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvolutionText extends Model
{
    protected $fillable = [
        'evolution_id',
        'text',
    ];

    public function evolution(): BelongsTo
    {
        return $this->belongsTo(Evolution::class);
    }

}
