<?php

declare(strict_types=1);

namespace App\Util;

use App\DTO\RawLogDTO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

final class RawLogsWriter
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    /** @throws Exception */
    public function write(RawLogDTO $rawLow): void
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection();

        $connection->insert(
            'raw_logs',
            [
                'requested_at' => $rawLow->requestedAt,
                'responded_at' => $rawLow->respondedAt,
                'status' => $rawLow->status,
                'request_headers' => $rawLow->requestHeaders,
                'request_body' => $rawLow->requestBody,
                'response_headers' => $rawLow->responseHeaders,
                'response_body' => $rawLow->responseBody,
            ]
        );
    }

    /**
     * @param RawLogDTO[] $logs
     * @throws Exception
     */
    public function writeBatch(array $rawLogs): void
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection();

        if (empty($rawLogs)) {
            return;
        }

        $placeholders = [];
        $values = [];
        foreach ($rawLogs as $rawLog) {
            $values[] = $rawLog->requestedAt;
            $values[] = $rawLog->respondedAt;
            $values[] = $rawLog->status;
            $values[] = $rawLog->requestHeaders;
            $values[] = $rawLog->requestBody;
            $values[] = $rawLog->responseHeaders;
            $values[] = $rawLog->responseBody;

            $placeholders[] = '?, ?, ?, ?, ?, ?, ?';
        }

        $columns = [
            'requested_at',
            'responded_at',
            'status',
            'request_headers',
            'request_body',
            'response_headers',
            'response_body',
        ];

        /** @noinspection SqlInsertValues */
        $connection->executeStatement(
            sprintf(
                'INSERT INTO raw_logs (%s) VALUES (%s)',
                implode(', ', $columns),
                implode('), (', $placeholders),
            ),
            $values,
        );
    }
}
