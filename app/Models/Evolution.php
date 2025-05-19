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
        'created_by',
        'observation',
        'evolution_text',
        'ai_suggestion'
    ];



    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

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
        static::saving(function (Evolution $evolution) {
            $evolution->created_by = auth()->id();
//            $evolution->evolution_text = $evolution->generateEvolutionText();
        });

    }

    public function generateEvolutionText($biometricData = null): string
    {
        $this->loadMissing([
            'assessmentOptions.assessmentGroup',
            'patient',
            'biometricData',
        ]);


        $biometricData = $biometricData ?? $this->biometricData;


        // Busca direto no banco, filtrando pelo evolution_id
        $metrics = CalculatedMetric::where('evolution_id', $this->id)->get();

        $metricAbbreviations = [
            'BMI' => 'BMI',
            'BloodPressure' => 'BP',
            'HeartRate' => 'HR',
            'RespiratoryRate' => 'RR',
            'OxygenSaturation' => 'OS',
            'Temperature' => 'TP',
        ];

        $metricsInterpretations = $metrics->mapWithKeys(function ($metric) use ($metricAbbreviations) {
            $calculatedType = $metric->calculated_type instanceof \BackedEnum
                ? $metric->calculated_type->value
                : (string) $metric->calculated_type;

            $abbr = $metricAbbreviations[$calculatedType] ?? $calculatedType;

            return ['{' . $abbr . '}' => $metric->interpretation];
        })->toArray();


        logger()->info('Metrics:', $metrics->toArray());
        logger()->info('MetricsInterpretations:', $metricsInterpretations);

        $dados = [];

        foreach ($this->assessmentOptions as $option) {
            $dados['{' . $option->assessmentGroup->slug . '}'] = $option->description;
        }



        $patientMap = [
            '{name}' => $this->patient->name,
            '{sex}' => $this->patient->sex?->value ?? 'Não Informado',
            '{age}' => $biometricData?->age ?? 'Não Informado',
            '{responsible}' => $this->patient->responsible ?? 'Não Informado',
            '{movement}' => $this->patient->movement ?? 'Não Informado',
            '{complaints}' => $this->patient->complaints ?? 'Não Informado',
            '{diagnosis}' => $this->patient->diagnosis ?? 'Não Informado',
            '{internment_reason}' => $this->patient->internment_reason ?? 'Não Informado',
        ];

        $dados = array_merge($dados, $patientMap, $metricsInterpretations);

        $template = "Cliente {name}, {age} anos, {sex}, admitida neste setor por HD : {diagnosis}, proveniente de : {internment_reason}, {movement} e acompanhado por {responsible}.
            Segue em {estado-geral}, {consciencia}, {orientacao}, {comunicacao}, {humorestado-emocional}, {hidratacao}, {estado-nutricional}, {pele}, {higiene-corporal}.
            Apresenta IMC :{BMI}, com pressão arterial - {BP}, frequência cardíaca - {HR}, frequência respiratória - {RR}, saturação - {OS} e temperatura - {TP}.";

        return strtr($template, $dados);
    }




}
