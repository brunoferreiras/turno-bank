<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function format(float $value, string $currency = 'USD'): string
    {
        return number_format($value, 2) . ' ' . $currency;
    }

    public static function formatToDatabase(float $value): int
    {
        return $value * 100;
    }
}
