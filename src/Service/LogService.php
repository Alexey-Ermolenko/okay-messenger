<?php

declare(strict_types=1);

namespace App\Service;

use App\Util\LogsReader;
use Doctrine\DBAL\Exception;

final class LogService
{
    public function __construct(
        private readonly LogsReader $logReader,
    ) {
    }

    /**
     * @throws Exception
     */
    public function findUserLogsByUserId(string $id): array
    {
        return $this->logReader->findUsersById($id);
    }

    /**
     * @throws Exception
     */
    public function findNotificationLogsByUserId(string $id): array
    {
        return $this->logReader->findNotificationLogsByUserId($id);
    }

    /**
     * @throws Exception
     */
    public function findRequestsLogs(\DateTimeInterface $since): array
    {
        return $this->logReader->findRequestsLogs($since);
    }
}
