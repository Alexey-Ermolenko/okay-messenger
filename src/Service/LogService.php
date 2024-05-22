<?php

declare(strict_types=1);

namespace App\Service;

use App\Util\LogsReader;
use DateTimeInterface;
use Exception;

final class LogService
{
    public function __construct(
        private readonly LogsReader $logReader,
    ) {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function findUserLogsByUserId(string $id): array
    {
        return $this->logReader->findUsersById($id);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function findNotificationLogsByUserId(string $id): array
    {
        return $this->logReader->findNotificationLogsByUserId($id);
    }

    public function findRequestsLogs(string $id, DateTimeInterface $since): array
    {
        return $this->logReader->findRequestsLogs($id, $since);
    }
}
