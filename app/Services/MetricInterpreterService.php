<?php

namespace App\Services;

use App\Models\CalculatedMetric;
use App\Enums\MetricType;
use Illuminate\Support\Facades\Log;

class MetricInterpreterService
{
    public static function handle(array $data, int $evolutionId): void
    {

        /// imc
        if (!empty($data['weight']) && !empty($data['height'])) {
            $height = floatval($data['height']);
            $weight = floatval($data['weight']);

            if (CalculatedMetric::where('evolution_id', $evolutionId)
                ->where('calculated_type', MetricType::BMI)
                ->exists()) {
                return;
            }
            $bmi = self::calculateBMI($data['weight'], $data['height']);
            $interpretation = self::interpretBMI($bmi);

            CalculatedMetric::create([
                'evolution_id' => $evolutionId,
                'calculated_type' => MetricType::BMI,
                'result' => round($bmi, 2),
                'interpretation' => $interpretation,
            ]);

        }



        //pressão arterial








        //frequencia cardiaca







        //frequencia respiratoria






        //saturacao





        //temperatura



    }


    private static function calculateBMI(float $width, float $height): float
    {
        if ($height > 3){
            $height = $height / 100;
        }
        return $width / ($height * $height);

    }

    private static function interpretBmi(float $bmi): string
    {
        return match (true) {
            $bmi < 18.5 => 'Abaixo do peso',
            $bmi < 25.0 => 'Peso normal',
            $bmi < 30.0 => 'Sobrepeso',
            $bmi < 35.0 => 'Obesidade Grau I',
            $bmi < 40.0 => 'Obesidade Grau II',
            $bmi >= 40.0 => 'Obesidade Grau III',
            default => 'Acima dos intervalos categorizados',
        };
    }
}
