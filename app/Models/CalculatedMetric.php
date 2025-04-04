<?php

namespace App\Models;

use App\Enums\MetricType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalculatedMetric extends Model
{
    protected $fillable = [
        'evolution_id',
        'calculated_type',
        'result',
        'interpretation',
    ];

    protected $casts = [
        'calculated_type' => MetricType::class,
    ];

    public function evolution(): belongsTo
    {
        return $this->belongsTo(Evolution::class);
    }
}
