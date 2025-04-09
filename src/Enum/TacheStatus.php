<?php

namespace App\Enum;

enum TacheStatus: string
{
    case To_do = 'to do';
    case Doing = 'doing';
    case Done = 'done';
    
    
    public function getLabel(): string
    {
        return match ($this) {
            self::To_do => 'To do',
            self::Doing => 'Doing',
            self::Done => 'Done',
            
        };
    }
}