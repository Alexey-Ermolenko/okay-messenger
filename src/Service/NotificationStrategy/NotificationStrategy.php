<?php

declare(strict_types=1);

namespace App\Service\NotificationStrategy;

use App\Entity\User;

class NotificationStrategy
{
	private NotificationStrategyInterface $notificationStrategy;

	public function __construct(NotificationStrategyInterface $notificationStrategy)
	{
		$this->notificationStrategy = $notificationStrategy;
	}

	public function execute(User $sender, User $recipient): string
	{
		return $this->notificationStrategy->sendNotification($sender, $recipient);
	}
}
