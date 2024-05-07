<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Log;
use App\Repository\LogRepository;
use DateTimeImmutable;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class ModelOperationSubscriber implements EventSubscriber
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly LogRepository $logRepository,
        private readonly EntityManagerInterface $entityManager,
        private array $logBuffer = []
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->logOperation('Created', $args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->logOperation('Updated', $args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->logOperation('Removed', $args);
    }

    private function logOperation(string $operation, LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        // Log the operation
        $this->logger->info(sprintf('%s %s entity', $operation, get_class($entity)));

        $log = new Log();
        $log->setLevel('info');
        $log->setChannel('doctrine');
        $log->setDatetime(new DateTimeImmutable());
        $log->setMessage(sprintf('%s %s entity', $operation, get_class($entity)));
        $log->setContext(['entity' => get_class($entity), 'operation' => $operation]);

        // Сохранение лога в базу данных
        #$this->logRepository->saveAndCommit($log);
        #$this->logRepository->close();

        #$this->entityManager->persist($log);
        #//$this->entityManager->flush();
        #$this->entityManager->clear();
        #$this->entityManager->close();

//        $this->entityManager->persist($log);
//        $this->entityManager->flush();
    }
}
