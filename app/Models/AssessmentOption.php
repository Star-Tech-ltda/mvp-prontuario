<?php

namespace App\Models;

use App\Enums\Severity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentOption extends Model
{
    protected $fillable = [
        'assessment_group_id',
        'description',
        'custom_phrase',
        'severity',
    ];

    protected $casts = [
        'severity' => Severity::class,
    ];

    public function assessmentGroup(): belongsTo
    {
        return $this->belongsTo(AssessmentGroup::class);
    }
}
