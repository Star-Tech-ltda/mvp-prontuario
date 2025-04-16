<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\EvolutionChecklist;
class Evolution extends Model
{
    protected $fillable = [
        'patient_id',
        'observation',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    public function assessmentOptions(): BelongsToMany
    {
        return $this->belongsToMany(AssessmentOption::class, 'evolution_checklist')
            ->using(EvolutionChecklist::class)
            ->withTimestamps();
    }

    public function evolutionChecklists(): HasMany
    {
        return $this->hasMany(EvolutionChecklist::class);
    }

    public function calculatedMetrics(): HasMany
    {
        return $this->hasMany(CalculatedMetric::class);
    }

    public function biometricData(): HasOne
    {
        return $this->hasOne(BiometricData::class);
    }

    public function checklistOptions(): belongsToMany
    {
        return $this->belongsToMany(AssessmentOption::class,
            'evolution_checklist',
            'evolution_id',
            'assessment_option_id'
        );
    }
    public function text(): HasOne
    {
        return $this->hasOne(EvolutionText::class);
    }


}
