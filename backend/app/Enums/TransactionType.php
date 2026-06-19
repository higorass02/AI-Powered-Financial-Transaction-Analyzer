<?php

namespace App\Enums;

enum TransactionType: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
    case PIX = 'pix';

    public function label(): string
    {
        return match($this) {
            self::DEBIT => 'Débito',
            self::CREDIT => 'Crédito',
            self::PIX => 'Pix',
        };
    }

    public function isOutgoing(): bool
    {
        return match($this) {
            self::DEBIT => true,
            self::CREDIT => false,
            self::PIX => false,
        };
    }
}
