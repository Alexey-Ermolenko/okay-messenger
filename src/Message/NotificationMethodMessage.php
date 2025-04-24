<?php

declare(strict_types=1);

namespace App\Message;

final readonly class NotificationMethodMessage
{
    public function __construct(
        private string $preferredNotificationMethod,
        private ?string $telegramAccountLink,
        private ?string $phoneNumber,
        private ?string $email,
    ) {
    }

    public function getTelegramAccountLink(): string
    {
        return $this->telegramAccountLink;
    }

    public function getPreferredNotificationMethod(): string
    {
        return $this->preferredNotificationMethod;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
