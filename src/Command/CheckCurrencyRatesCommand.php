<?php

namespace App\Command;

use App\Notification\NotificationInterface;
use App\Service\BankCurrencyRateService;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;

#[AsCommand(
    name: 'app:check-currency-rates',
    description: 'Checks exchange rates from PrivatBank and Monobank and notifies about changes.',
)]
class CheckCurrencyRatesCommand extends Command
{
    public function __construct(
        private readonly BankCurrencyRateService $bankCurrencyRateService,
        private readonly NotificationInterface $notification,
        private readonly float $threshold = 0.05
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'threshold',
            InputArgument::OPTIONAL,
            'Enter the % difference for the threshold. The default value is 5%.'
        );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[NoReturn] protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $threshold = $io->ask(
            'Enter a threshold value (or press Enter for default: '.$this->threshold * 100 .'%):',
            $this->threshold * 100
        );

        $threshold = $this->validateThreshold($threshold);

        $mono = $this->bankCurrencyRateService->getRate(
            BankCurrencyRateService::MONO_BANK_URL
        );

        $privat = $this->bankCurrencyRateService->getRate(
            BankCurrencyRateService::PRIVAT_BANK_URL
        );

        $result = $this->bankCurrencyRateService->compareRates($privat, $mono, $threshold);

        if ($result->getIsChanged()) {
            // send SMS notification as default
            $this->notification->send($result->getMessage());
        }

        $io->info($result->getMessage());

        return Command::SUCCESS;
    }

    private function validateThreshold($threshold): float
    {
        if (!empty($threshold) && !is_numeric($threshold)) {
            throw new InvalidArgumentException('The parameter must be a valid integer.');
        }

        if (!empty($threshold) && $threshold < 0) {
            throw new InvalidArgumentException('The parameter must be greater than or equal to zero.');
        }

        return (float)$threshold / 100;
    }
}