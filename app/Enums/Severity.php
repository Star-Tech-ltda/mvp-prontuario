<?php

namespace App\Enums;

enum Severity: string
{
    case NONE = 'None';
    case LOW = 'Low';
    case MEDIUM = 'Medium';
    case HIGH = 'High';
    case CRITICAL = 'Critical';

    public function label():string
    {
        return match ($this) {
            self::NONE=>'Nenhum',
            self::LOW=>'Baixo',
            self::MEDIUM=>'Médio',
            self::HIGH=>'Alto',
            self::CRITICAL=>'Crítico',
        };
    }
}

