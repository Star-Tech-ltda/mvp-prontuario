<?php

namespace App\Enums;

enum MetricType: string
{
    case BMI = 'BMI';
    case BP = 'BloodPressure';
    case HR = 'HeartRate';
    case RR = 'RespiratoryRate';
    case OS = 'OxygenSaturation';
    case TP = 'Temperature';

    public function label(): string
    {
        return match ($this) {
            self::BMI => 'IMC',
            self::BP => 'Pressão Arterial',
            self::HR => 'Frequência Cardíaca',
            self::RR => 'Frequência Respiratória',
            self::OS => 'Saturação',
            self::TP => 'Temperatura',
        };
    }
}
