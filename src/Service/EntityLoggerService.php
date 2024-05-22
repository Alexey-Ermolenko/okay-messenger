<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\LogDTO;
use App\Entity\User;
use App\Util\LogsWriter;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use \Symfony\Bundle\SecurityBundle\Security;

final class EntityLoggerService
{
    private const DATETIME_FORMAT = 'Y-m-d H:i:s';
    public const INSERT = 'INSERT';
    public const UPDATE = 'UPDATE';
    public const DELETE = 'DELETE';

    public function __construct(
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly LogsWriter $logsWriter,
    ) {
    }

    /**
     * @throws Exception
     */
    public function log(string $entityType, string $entityId, string $action, array $eventData): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $record = new LogDTO(
            id: null,
            entityType: $entityType,
            entityId: (int)$entityId,
            createdAt: (new \DateTimeImmutable)->format(self::DATETIME_FORMAT),
            user_id: $user->getId(),
            action: $action,
            requestRoute: $request->get('_route'),
            data: json_encode($eventData),
            ipAddress: $request->getClientIp()
        );

        $this->logsWriter->write($record);
    }
}
