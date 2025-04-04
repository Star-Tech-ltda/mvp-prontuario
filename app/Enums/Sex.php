<?php

namespace App\Enums;

enum Sex: string
{
    case MALE = 'Male';
    case FEMALE = 'Female';

    public function label(): string
    {
        return match ($this) {
            self::MALE=>'Masculino',
            self::FEMALE=>'Feminino',
        };
    }
}
