<?php

namespace App\Service;

use App\Notification\NotificationInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class EmailNotification implements NotificationInterface
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(string $message): void
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to('user@example.com')
            ->subject('New Rate changes')
            ->text($message);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new TransportException($e->getMessage());
        }
    }
}