<?php

namespace App\Enums;

enum DepositStatus: int
{
    case PENDING = 0;
    case ACCEPTED = 1;
    case REJECTED = 2;

    public static function fromStatus(string $status): self
    {
        return match ($status) {
            'pending' => self::PENDING,
            'accepted' => self::ACCEPTED,
            'rejected' => self::REJECTED,
        };
    }
}
