<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case FAILED = 'failed';
    case CANCELED = 'canceled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::APPROVED => 'Aprovado',
            self::FAILED => 'Falhou',
            self::CANCELED => 'Cancelado',
        };
    }

    public function isActive(): bool
    {
        return $this === self::APPROVED || $this === self::PENDING;
    }
}
