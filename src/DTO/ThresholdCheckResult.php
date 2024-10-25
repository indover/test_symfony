<?php

namespace App\DTO;

class ThresholdCheckResult
{
    public function __construct(
        private bool $isChanged,
        private float $threshold,
        private readonly array $message,
    ) {
        $this->isChanged = $isChanged;
    }

    public function getIsChanged(): bool
    {
        return $this->isChanged;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return implode("\n", $this->message);
    }
}