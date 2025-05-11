<?php

namespace App\Enums;

enum CostType :string
{
    case UN = 'Unity';
    case USE = 'Use';
    case SER = 'Service';
    case PER = 'Percent';
    case KM = 'Kilometer';
    case TM = 'Time';
    case MON = 'Month';
    case FX = 'Fixed';

    public function label(): string
    {
        return match ($this) {
            self::UN=>'Unidade',
            self::USE=>'Uso',
            self::SER=>'Serviço',
            self::PER=>'Porcentagem',
            self::KM=>'Quilômetro',
            self::TM=>'Hora',
            self::MON=>'Mes',
            self::FX=>'Fixo',
        };
    }

    public function pluralLabel(): string
    {
        return match ($this) {
            self::UN => 'Unidades',
            self::USE => 'Usos',
            self::SER => 'Serviços',
            self::PER => 'Porcentagens',
            self::KM => 'Quilômetros',
            self::TM => 'Horas',
            self::MON => 'Meses',
            self::FX => 'Fixos',
        };
    }
}
