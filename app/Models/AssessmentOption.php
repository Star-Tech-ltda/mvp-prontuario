<?php

namespace App\Models;

use App\Enums\Severity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssessmentOption extends Model
{
    protected $fillable = [
        'assessment_group_id',
        'description',
        'severity',
    ];

    protected $casts = [
        'severity' => Severity::class,
    ];

    public function assessmentGroup(): belongsTo
    {
        return $this->belongsTo(AssessmentGroup::class);
    }

    public function evolutions(): BelongsToMany
    {
        return $this->belongsToMany(Evolution::class, 'evolution_checklists');
    }

}
