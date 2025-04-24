<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\NotificationMethodChangedEvent;
use App\Message\NotificationMethodMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationMethodChangedListener
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function onNotificationMethodChanged(NotificationMethodChangedEvent $event): void
    {
        // Sending to telegram service for matching userLink and userId for further sending of messages
        $message = new NotificationMethodMessage(
            $event->getPreferredNotificationMethod(),
            $event->getTelegramAccountLink(),
            $event->getPhoneNumber(),
            $event->getEmail(),
        );

        $this->messageBus->dispatch($message);

        // Sending to emailService for notify the user that the preferred method of communication has been switched
        // and further sending of the bot for mailing
        //        $telegramNotificationsTypeMessage = new SettedTelegramNotificationsTypeMessage(
        //            $event->getTelegramAccountLink(),
        //            $event->getPreferredNotificationMethod(),
        //        );
        //        $this->messageBus->dispatch($telegramNotificationsTypeMessage);
    }
}
