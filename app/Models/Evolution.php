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
        'evolution_text'
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
    protected static function booted(): void
    {
        static::saving(static function (Evolution $evolution) {
            $evolution->generateEvolutionText();
        });
    }

    public function generateEvolutionText(): string
    {
        $this->loadMissing('assessmentOptions.assessmentGroup');

        $evoText = '';

        $templates = [
            'abdome' => 'O abdome apresenta-se :option.',
            'boca' => 'Observa-se na boca: :option.',
            'cranio' => 'O crânio mostra-se :option.',
            'olhos' => 'Nos olhos, nota-se: :option.',
            'ouvidos' => 'Nos ouvidos, evidencia-se: :option.',
        ];

        $groups = AssessmentGroup::with('assessmentOptions')->get();

        foreach ($groups as $group) {
            $selectedOption = $this->assessmentOptions
                ->filter(function ($option) use ($group) {
                    return $option->assessmentGroup && $option->assessmentGroup->id === $group->id;
                })
                ->first();

            if ($selectedOption) {
                $template = $templates[$group->slug] ?? 'O estado de ' . $group->name . ' é :option.';
                $evoText .= str_replace(':option', $selectedOption->description, $template) . ' ';
            }
        }

        return trim($evoText);
    }


}
