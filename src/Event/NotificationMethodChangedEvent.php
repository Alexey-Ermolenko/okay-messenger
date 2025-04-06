<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class NotificationMethodChangedEvent extends Event
{
    public function __construct(
        private readonly string $preferredNotificationMethod,
        private readonly ?string $telegramAccountLink,
        private readonly ?string $phoneNumber,
        private readonly ?string $email,
    ) {
    }

    public function getPreferredNotificationMethod(): string
    {
        return $this->preferredNotificationMethod;
    }

    public function getTelegramAccountLink(): ?string
    {
        return $this->telegramAccountLink;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}