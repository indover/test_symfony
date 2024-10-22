<?php

namespace App\Notification;

interface NotificationInterface
{
    public function send(string $message);
}