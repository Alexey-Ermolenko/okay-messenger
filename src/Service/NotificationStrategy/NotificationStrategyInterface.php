<?php

declare(strict_types=1);

namespace App\Service\NotificationStrategy;

use App\Entity\User;

interface NotificationStrategyInterface
{
	public function sendNotification(User $sender, User $recipient): string;
}
