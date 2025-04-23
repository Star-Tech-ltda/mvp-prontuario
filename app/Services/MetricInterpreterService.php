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

                $interpretation = self::interpretHR($hr, $age);

                CalculatedMetric::create([
                    'evolution_id' => $evolutionId,
                    'calculated_type' => MetricType::HR,
                    'result' => $hr,
                    'interpretation' => $interpretation,
                ]);
            }
        }

        //frequencia respiratoria
        if (!empty($data['respiratory_rate']) && !empty($data['age'])) {
            $rr = intval($data['respiratory_rate']);
            $age = intval($data['age']);

            if (!CalculatedMetric::where('evolution_id', $evolutionId)
                ->where('calculated_type', MetricType::RR)
                ->exists()) {

                $interpretation = self::interpretRR($rr, $age);

                CalculatedMetric::create([
                    'evolution_id' => $evolutionId,
                    'calculated_type' => MetricType::RR,
                    'result' => $rr,
                    'interpretation' => $interpretation,
                ]);
            }
        }




        //saturacao
        if (!empty($data['oxygen_saturation'])) {
            $oxygenSat = intval($data['oxygen_saturation']);

            if (!CalculatedMetric::where('evolution_id', $evolutionId)
                ->where('calculated_type', MetricType::OS)
                ->exists()) {

                $interpretation = self::interpretOS($oxygenSat);

                CalculatedMetric::create([
                    'evolution_id' => $evolutionId,
                    'calculated_type' => MetricType::OS,
                    'result' => $oxygenSat,
                    'interpretation' => $interpretation,
                ]);
            }
        }




        //temperatura
        if (!empty($data['temperature'])) {
            $temp = floatval($data['temperature']);

            if (!CalculatedMetric::where('evolution_id', $evolutionId)
                ->where('calculated_type', MetricType::TP)
                ->exists()) {

                $interpretation = self::interpretTP($temp);

                CalculatedMetric::create([
                    'evolution_id' => $evolutionId,
                    'calculated_type' => MetricType::TP,
                    'result' => $temp,
                    'interpretation' => $interpretation,
                ]);
            }
        }


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

    // frequencia respiratoria
    private static function interpretRR(int $rr, int $age): string
    {
        if ($age < 1) { // metricas para bebês
            return match (true) {
                $rr < 20 => 'Bradipneia severa',
                $rr <= 29 => 'Bradipneia',
                $rr <= 60 => 'Normal',
                $rr <= 70 => 'Taquipneia leve',
                $rr <= 80 => 'Taquipneia moderada',
                $rr > 80 => 'Taquipneia severa',
                default => 'Indeterminado',
            };
        } elseif ($age <= 12) { // metricas para crianças
            return match (true) {
                $rr < 15 => 'Bradipneia severa',
                $rr <= 19 => 'Bradipneia',
                $rr <= 30 => 'Normal',
                $rr <= 35 => 'Taquipneia leve',
                $rr <= 50 => 'Taquipneia moderada',
                $rr > 50 => 'Taquipneia severa',
                default => 'Indeterminado',
            };
        } else { // metricas para adulto
            return match (true) {
                $rr < 8 => 'Bradipneia severa',
                $rr <= 11 => 'Bradipneia',
                $rr <= 20 => 'Normal',
                $rr <= 24 => 'Taquipneia leve',
                $rr <= 30 => 'Taquipneia moderada',
                $rr > 30 => 'Taquipneia severa',
                default => 'Indeterminado',
            };
        }
    }

    //saturação
    private static function interpretOS(int $oxygenSat): string
    {
        return match (true) {
            $oxygenSat < 85 => 'Hipoxemia severa',
            $oxygenSat <= 89 => 'Hipoxemia moderada',
            $oxygenSat <= 94 => 'Hipoxemia leve',
            $oxygenSat >= 96 => 'Normal',
            default => 'Indeterminado',
        };
    }

    //temperatura
    private static function interpretTP(float $temp): string
    {
        return match (true) {
            $temp < 32.0 => 'Hipotermia severa',
            $temp <= 33.9 => 'Hipotermia moderada',
            $temp <= 35.9 => 'Hipotermia leve',
            $temp <= 37.5 => 'Normal',
            $temp <= 38.5 => 'Estado febril',
            $temp <= 39.5 => 'Febre moderada',
            $temp <= 40.9 => 'Febre alta',
            $temp >= 41.0 => 'Hipertermia/Febre muito alta',
            default => 'Indeterminado',
        };
    }

}
