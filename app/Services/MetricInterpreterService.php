<?php

namespace App\Services;

use App\Models\CalculatedMetric;
use App\Enums\MetricType;

class MetricInterpreterService
{
    public static function handle(array $data, int $evolutionId): void
    {
        // IMC
        if (!empty($data['weight']) && !empty($data['height'])) {
            $bmi = $data['weight'] / ($data['height'] * $data['height']);
            $interpretation = self::interpretBmi($bmi);

            CalculatedMetric::create([
                'evolution_id'     => $evolutionId,
                'calculated_type'  => MetricType::BMI,
                'result'           => round($bmi, 2),
                'interpretation'   => $interpretation,
            ]);
        }


    }

    private static function interpretBmi(float $bmi): string
    {
        return match (true) {
            $bmi < 18.5             => 'Abaixo do peso',
            $bmi < 25.0             => 'Peso normal',
            $bmi < 30.0             => 'Sobrepeso',
            default                 => 'Obesidade',
        };
    }
}
