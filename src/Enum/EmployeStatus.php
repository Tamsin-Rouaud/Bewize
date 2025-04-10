<?php

namespace App\Enum;

enum EmployeStatus: string
{
    case Cdi = 'Cdi';
    case Cdd = 'Cdd';
    case Freelance = 'Freelance';
    case Autres = 'Autres';
    
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