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
        if (!empty($data['systolic_pressure']) && !empty($data['diastolic_pressure'])) {
            $systolic = intval($data['systolic_pressure']);
            $diastolic = intval($data['diastolic_pressure']);

            if (!CalculatedMetric::where('evolution_id', $evolutionId)
                ->where('calculated_type', MetricType::BP)
                ->exists()) {

                $interpretation = self::interpretBP($systolic, $diastolic);

                CalculatedMetric::create([
                    'evolution_id' => $evolutionId,
                    'calculated_type' => MetricType::BP,
                    'result' => "{$systolic}/{$diastolic}",
                    'interpretation' => $interpretation,
                ]);
            }
        }



        //frequencia cardiaca

        if (!empty($data['heart_rate']) && !empty($data['age'])) {
            $hr = intval($data['heart_rate']);
            $age = intval($data['age']);

            if (!CalculatedMetric::where('evolution_id', $evolutionId)
                ->where('calculated_type', MetricType::HR)
                ->exists()) {

                $interpretation = self::interpretBP($hr, $age);

                CalculatedMetric::create([
                    'evolution_id' => $evolutionId,
                    'calculated_type' => MetricType::HR,
                    'result' => $hr,
                    'interpretation' => $interpretation,
                ]);
            }
        }





        //frequencia respiratoria






        //saturacao





        //temperatura



    }


    //imc
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

    // pressao arterial
    private static function interpretBP(int $systolic, int $diastolic): string
    {
        return match (true) {
            $systolic < 120 && $diastolic < 80 => 'Ótima',
            $systolic < 130 && $diastolic < 85 => 'Normal',
            $systolic < 140 && $diastolic < 90 => 'Normal-alta',
            $systolic < 160 && $diastolic < 100 => 'Hipertensão estágio 1',
            $systolic < 180 && $diastolic < 110 => 'Hipertensão estágio 2',
            $systolic >= 180 || $diastolic >= 110 => 'Hipertensão estágio 3',
            $systolic >= 140 && $diastolic < 90 => 'Hipertensão sistólica isolada',
            default => 'Indeterminado',
        };
    }

    //frequencia cardiaca
    private static function interpretHR(int $hr, int $age): string
    {
        if ($age < 1) { // metricas para bebês
            return match (true) {
                $hr < 70 => 'Bradicardia severa',
                $hr < 90 => 'Bradicardia',
                $hr <= 160 => 'Frequência normal',
                $hr <= 180 => 'Taquicardia leve',
                $hr <= 200 => 'Taquicardia moderada',
                $hr > 200 => 'Taquicardia severa',
                default => 'Indeterminado',
            };
        } elseif ($age <= 10) { // metricas para crianças
            return match (true) {
                $hr < 60 => 'Bradicardia severa',
                $hr < 70 => 'Bradicardia',
                $hr <= 120 => 'Frequência normal',
                $hr <= 150 => 'Taquicardia leve',
                $hr <= 180 => 'Taquicardia moderada',
                $hr > 180 => 'Taquicardia severa',
                default => 'Indeterminado',
            };
        } else { // metricas para adulto
            return match (true) {
                $hr < 40 => 'Bradicardia severa',
                $hr < 60 => 'Bradicardia',
                $hr <= 100 => 'Frequência normal',
                $hr <= 120 => 'Taquicardia leve',
                $hr <= 140 => 'Taquicardia moderada',
                $hr > 140 => 'Taquicardia severa',
                default => 'Indeterminado',
            };
        }
    }

}
