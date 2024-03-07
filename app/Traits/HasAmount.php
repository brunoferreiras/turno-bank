<?php

namespace App\Traits;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasAmount
{
    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => CurrencyHelper::formatToResponse($value),
            set: fn ($value) => CurrencyHelper::formatToDatabase($value)
        );
    }
}
