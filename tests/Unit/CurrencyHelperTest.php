<?php

namespace Tests\Unit;

use App\Helpers\CurrencyHelper;
use PHPUnit\Framework\TestCase;

class CurrencyHelperTest extends TestCase
{
    /** @test */
    public function it_should_format_a_currency_to_response(): void
    {
        $value = 10000;
        $currency = 'USD';
        $formatted = CurrencyHelper::formatToResponse($value, $currency);
        $this->assertEquals(100.00, $formatted);
    }

    /** @test */
    public function it_should_format_a_currency_to_database(): void
    {
        $value = 100;
        $formatted = CurrencyHelper::formatToDatabase($value);
        $this->assertEquals(10000, $formatted);
    }
}
