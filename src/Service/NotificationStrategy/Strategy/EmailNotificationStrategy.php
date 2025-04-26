<?php

declare(strict_types=1);

namespace App\Service\NotificationStrategy\Strategy;

use App\Entity\User;
use App\Message\EmailMessage;
use App\Service\NotificationStrategy\NotificationStrategyInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailNotificationStrategy implements NotificationStrategyInterface
{
	private MessageBusInterface $messageBus;

	public function __construct(MessageBusInterface $messageBus)
	{
		$this->messageBus = $messageBus;
	}

	public function sendNotification(User $sender, User $recipient): string
	{
		$msg = json_encode([
			'fromEmail' => $sender->getEmail(),
			'toEmail' => $recipient->getEmail(),
		]);

		$message = new EmailMessage($msg);
		$this->messageBus->dispatch($message);

		return $msg;
	}
}
