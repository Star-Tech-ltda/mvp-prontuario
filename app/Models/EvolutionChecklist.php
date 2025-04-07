<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EvolutionChecklist extends Pivot
{
    protected $fillable = [
        'evolution_id',
        'assessment_option_id',
    ];
    public $timestamps = true;

    public function evolution(): BelongsTo
    {
        return $this->belongsTo(Evolution::class);
    }

    public function assessmentOption(): BelongsTo
    {
        return $this->belongsTo(AssessmentOption::class);
    }
}
