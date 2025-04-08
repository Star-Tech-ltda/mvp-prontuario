<?php

namespace App\Enums;

enum MaritalStatus :string
{
    case SI = 'Single';
    case MA = 'Married';
    case DI = 'Divorced';
    case OT = 'Other';


    public function label(): string
    {
        return match ($this) {
            self::SI => 'Solteiro(a)',
            self::MA => 'Casado(a)',
            self::DI => 'Divorciado(a)',
            self::OT => 'Outro',

        };
    }
}
