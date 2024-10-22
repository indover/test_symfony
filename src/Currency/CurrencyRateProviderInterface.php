<?php

namespace App\Currency;

interface CurrencyRateProviderInterface
{
    public function getRate(string $bankAPIUrl): array;
}