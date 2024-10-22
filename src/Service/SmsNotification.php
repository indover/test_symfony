<?php

namespace App\Service;

use App\Notification\NotificationInterface;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

readonly class SmsNotification implements NotificationInterface
{
    private Client $twilioClient;

    /**
     * @throws ConfigurationException
     */
    public function __construct(string $accountSid, string $accountToken, private string $fromTwilioNumber)
    {
        $this->twilioClient = new Client($accountSid, $accountToken);
    }

    /**
     * @throws TwilioException
     */
    public function send(string $message): void
    {
        try {
            $this->twilioClient->messages->create(
                "+351929046366",
                [
                    "body" => $message,
                    "from" => $this->fromTwilioNumber,
                ]
            );
        } catch (TwilioException $e) {
            throw new TwilioException($e->getMessage());
        }
    }
}