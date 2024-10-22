<?php

namespace App\Service;

use AllowDynamicProperties;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;

#[AllowDynamicProperties] class TwilioService
{
    /**
     * @throws ConfigurationException
     */
    public function __construct(string $accountSid, string $authToken)
    {
        $this->client = new Client($accountSid, $authToken);
    }
}