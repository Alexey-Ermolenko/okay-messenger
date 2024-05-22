<?php

declare(strict_types=1);

namespace App\Util;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\Level;

final class LogsReader
{
    private const LIMIT = 100;

    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    /**
     *
     * @return array<int, array<string, mixed>>
     * @throws Exception
     */
    public function findNotificationLogsByUserId(?string $notificationId): array
    {
        if (!$notificationId) {
            return [];
        }

        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection();

        // todo:logs: `$internalId` is unused while the table not indexed
        //$criteria = implode(' OR ', array_filter([
        //    $paymentId ? 'l.payment_id = :paymentId' : null,
        //    $internalId ? 'l.internal_id = :internalId' : null,
        //]));
        //$parameters = array_filter([
        //    'paymentId' => $paymentId,
        //    'internalId' => $internalId,
        //]);


        /**
           select l.*
             from logs l
            where l.entity_type = 'Notification'
              and l.user_id = 1
            order by created_at desc;
         */
        return $connection->createQueryBuilder()
            ->select('l.*')
            ->from('logs', 'l')
            ->where(
                "l.entity_type = 'Notification'",
                'l.user_id = :user_id',
            )
            ->orderBy('l.created_at', 'DESC')
            ->setMaxResults(self::LIMIT)
            ->setParameters([
                'user_id' => 1
            ])
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function findUsersById(string $userId, ?DateTimeImmutable $sinceDatetime = null): array
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection();

        /**
        select l.*
        from logs l
        where
              l.entity_type = 'User'
          and l.entity_id = 1
          and l.user_id = 1
        order by created_at desc;

         */

        return $connection->createQueryBuilder()
            ->select('l.*')
            ->from('logs', 'l')
            ->where(
                "l.entity_type = 'User'",
                //'l.entity_id = :entity_id',
                'l.user_id = :user_id',
            )
            ->orderBy('l.created_at', 'DESC')
            ->setMaxResults(self::LIMIT)
            ->setParameters(
                [
                    //'entity_id' => 1,
                    'user_id' => $userId
                ]
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function findRequestsLogs(string $id, DateTimeImmutable $sinceDatetime): array
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection();

        return $connection->createQueryBuilder()
            ->select('*')
            ->from('raw_logs')
            ->where(
                "channel = 'http_api_requests'",
                'datetime > :since',
            )
            ->orderBy('id', 'DESC')
            ->setMaxResults(self::LIMIT)
            ->setParameters(
                [
                    'level' => Level::Notice->getName(),
                    'since' => $sinceDatetime->format(DateTimeInterface::ATOM),
                ],
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
