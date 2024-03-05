<?php

namespace App\Enums;

enum UserTypes: string
{
    case CUSTOMER = 'customer';
    case ADMIN = 'admin';

    public function getGate(): string
    {
        return match ($this) {
            self::ADMIN => 'admin-user',
            self::CUSTOMER => 'customer-user'
        };
    }
}
