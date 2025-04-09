<?php

namespace App\Enum;

enum EmployeStatus: string
{
    case Cdi = 'cdi';
    case Cdd = 'cdd';
    case Freelance = 'freelance';
    case Autres = 'autres';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::Cdi => 'CDI',
            self::Cdd => 'CDD',
            self::Freelance => 'Freelance',
            self::Autres => 'Autres',
        };
    }
}