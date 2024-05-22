<?php

declare(strict_types=1);

namespace App\Util;

use App\DTO\LogDTO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

final class LogsWriter
{
    private const PGSQL = 'postgresql';

    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    /** @throws Exception */
    public function write(LogDTO $log): void
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection();

        $connection->insert(
            'logs',
            [
                'entity_type' => $log->entityType,
                'entity_id'   => $log->entityId,
                'action'      => $log->action,
                'data'        => $log->data,
                'created_at'  => $log->createdAt,
                'user_id'     => $log->user_id,
                'ip_address'  => $log->ipAddress,
                'route'       => $log->requestRoute,
            ],
        );
    }

    /**
     * @param LogDTO[] $logs
     * @throws Exception
     */
    public function writeBatch(array $logs): void
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection(self::PGSQL);

        if (empty($logs)) {
            return;
        }

        $placeholders = [];
        $values = [];
        foreach ($logs as $log) {
            $values[] = $log->entityType;
            $values[] = $log->entityId;
            $values[] = $log->action;
            $values[] = $log->data;
            $values[] = $log->createdAt;
            $values[] = $log->user_id;
            $values[] = $log->ipAddress;
            $values[] = $log->requestRoute;

            $placeholders[] = '?, ?, ?, ?, ?, ?, ?, ?';
        }

        $columns = [
            'entity_type',
            'entity_id',
            'action',
            'data',
            'created_at',
            'user_id',
            'ip_address',
            'route',
        ];

        /** @noinspection SqlInsertValues */
        $connection->executeStatement(
            sprintf(
                'INSERT INTO logs (%s) VALUES (%s)',
                implode(', ', $columns),
                implode('), (', $placeholders),
            ),
            $values,
        );
    }
}
