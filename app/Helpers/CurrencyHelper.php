<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function formatToResponse(int $value): float
    {
        return $value / 100;
    }

    public static function formatToDatabase(float $value): int
    {
        return $value * 100;
    }
}
