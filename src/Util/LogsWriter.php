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
                'level' => $log->level,
                'channel' => $log->channel,
                'datetime' => $log->datetime,
                'message' => $log->message,
                'context' => $log->context,
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
            $values[] = $log->level;
            $values[] = $log->channel;
            $values[] = $log->datetime;
            $values[] = $log->message;
            $values[] = $log->context;
            $placeholders[] = '?, ?, ?, ?, ?';
        }

        $columns = [
            'level',
            'channel',
            'datetime',
            'message',
            'context',
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
