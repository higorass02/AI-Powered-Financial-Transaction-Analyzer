<?php

namespace App\Enums;

enum AnomalyLevel: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Baixo',
            self::MEDIUM => 'Médio',
            self::HIGH => 'Alto',
        };
    }

    public function threshold(): float
    {
        return match($this) {
            self::LOW => 2.0,
            self::MEDIUM => 3.5,
            self::HIGH => 5.0,
        };
    }
}
