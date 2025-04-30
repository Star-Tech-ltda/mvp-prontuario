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

    protected static function booted(): void
    {
        static::saving(static function (Evolution $evolution) {
            $evolution->generateEvolutionText();
        });
    }

    public function generateEvolutionText(): string
    {


        $this->loadMissing([
            'assessmentOptions.assessmentGroup',
            'patient',
        ]);

        $biometricData = \DB::table('biometric_data')
            ->where('evolution_id', $this->id)
            ->first();


        $dados = [];

        foreach ($this->assessmentOptions as $option) {
            $dados['{' . $option->assessmentGroup->slug . '}'] = $option->description;
        }

            $patientMap=[
                '{name}' => $this->patient->name,
                '{sex}' => $this->patient->sex?->value ?? 'Não Informado',
                '{age}' => $biometricData?->age ?? 'Não Informado',
                '{responsible}' => $this->patient->responsible ?? 'Não Informado',
                '{movement}' => $this->patient->movement ?? 'Não Informado',
                '{complaints}' => $this->patient->complaints ?? 'Não Informado',
                '{diagnosis}' => $this->patient->diagnosis ?? 'Não Informado',
                '{internment-reason}' => $this->patient->internment_reason ?? 'Não Informado',
            ] ;


        $dados = array_merge($dados, $patientMap);


        $template = "Cliente {name}, {age} anos, {sex}, admitida neste setor por HD : {diagnosis}, proveniente de {internment_reason}, {movement} e acompanhado por {responsible}.
                Segue em {estado-geral}, {consciencia}, {orientacao}, {comunicação}, {humorestado-emocional}, {hidratação}, {estado-nutricional}, {pele}, {higiene corporal}.
                .";



        return strtr($template, $dados);
    }



}
