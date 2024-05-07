<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Log;
use DateInterval;
use DateTimeImmutable;
use \App\Service\DatetimeServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

final class LogService
{
    private const EXCLUDED_CHANNELS = ['doctrine'];
    private const DEFAULT_TIMEFRAME = 'P2D';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private RequestStack $requestStack
    ) {
    }

    public function log(string $entityType, string $entityId, string $action, array $eventData): void
    {
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();
        $log = new Log();

        $log->setMessage();
        $log->setDatetime(new DateTimeImmutable());
        $log->setChannel();
        $log->setContext();
        $log->setLevel();
        /*
            $log->setEntityType($entityType);
            $log->setEntityId($entityId);
            $log->setAction($action);
            $log->setEventData($eventData);
            $log->setUser($user);
            $log->setRequestRoute($request->get('_route'));
            $log->setIpAddress($request->getClientIp());
            $log->setCreatedAt(new DateTimeImmutable);
        */
        $this->em->persist($log);
        $this->em->flush();
    }


    public function findRequestsLogs(?DateTimeImmutable $sinceDatetime = null): array
    {
//        $date = $sinceDatetime ?? $this->datetimeService
//            ->now()
//            ->sub(new DateInterval(self::DEFAULT_TIMEFRAME));

        return [];
//        return $this->logsReader->findSlowRequestsLogs(
//            $sinceDatetime ?? $this->datetimeService
//            ->now()
//            ->sub(new DateInterval(self::DEFAULT_TIMEFRAME)),
//        );
    }
}
