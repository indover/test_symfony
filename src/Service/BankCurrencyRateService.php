<?php

namespace App\Service;

use App\Currency\CurrencyRateProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class BankCurrencyRateService implements CurrencyRateProviderInterface
{
    const string PRIVAT_BANK_URL = 'https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5';
    const string MONO_BANK_URL = 'https://api.monobank.ua/bank/currency';
    const string USD_CURRENCY = 'USD';
    const string EUR_CURRENCY = 'EUR';
    const string UAH_CURRENCY = 'UAH';
    const int USD_CURRENCY_CODE = 840;
    const int EUR_CURRENCY_CODE = 978;
    const int UAH_CURRENCY_CODE = 980;

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getRate(string $bankAPIUrl): array
    {
        $response = $this->httpClient->request(Request::METHOD_GET, $bankAPIUrl);
        $rates = $response->toArray();

        if ($bankAPIUrl == self::MONO_BANK_URL) {
            return array_slice($rates, 0, 2);
        }

        return $rates;
    }

    public function getCurrencyCode($ccy): int
    {
        $currencyCodes = [
            self::USD_CURRENCY => self::USD_CURRENCY_CODE,
            self::EUR_CURRENCY => self::EUR_CURRENCY_CODE,
            self::UAH_CURRENCY => self::UAH_CURRENCY_CODE,
        ];

        return $currencyCodes[$ccy];
    }

    public function compareRates($privat, $mono, $threshold): array
    {
        $messages = [];

        foreach ($privat as $privatRate) {
            foreach ($mono as $monoRate) {
                $privatCurrencyCode = $this->getCurrencyCode($privatRate['ccy']);
                if ($privatCurrencyCode === $monoRate['currencyCodeA']) {
                    $privatBuy = floatval($privatRate['buy']);
                    $monoBuy = floatval($monoRate['rateBuy']);

                    if (abs($privatBuy - $monoBuy) >= $threshold) {
                        $messages[] = "The exchange rate difference for currency {$privatRate['ccy']}: PrivatBank ($privatBuy), Monobank ($monoBuy)";
                    } else {
                        $messages[] = "The rates are the same or the difference is insignificant.";
                    }
                }
            }
        }

        return $messages;
    }

    public function getMessage(array $messages): string
    {
        if (str_starts_with($messages[0], "The rates")) {
            return array_shift($messages);
        } else {
            return implode(PHP_EOL, $messages);
        }
    }
}