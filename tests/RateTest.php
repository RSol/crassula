<?php


namespace App\Tests;


use App\Entity\Rate;
use PHPUnit\Framework\TestCase;

class RateTest extends TestCase
{
    public function testGetParamsBySource(): void
    {
        $currency = 'USD';

        $europa = Rate::getParamsBySource(Rate::SOURCE_EUROPA, $currency);
        $this->assertEquals([
            'source' => Rate::SOURCE_EUROPA,
            'from_currency' => 'EUR',
            'to_currency' => $currency,
        ], $europa);

        $cbr = Rate::getParamsBySource(Rate::SOURCE_CBR, $currency);
        $this->assertEquals([
            'source' => Rate::SOURCE_CBR,
            'from_currency' => $currency,
            'to_currency' => 'RUB',
        ], $cbr);
    }
}