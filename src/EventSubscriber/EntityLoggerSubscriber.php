<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\EntityLoggerService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class which listens on Doctrine events and writes an audit log of any entity changes made via Doctrine.
 */
#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
#[AsDoctrineListener(event: Events::postRemove)]
class EntityLoggerSubscriber implements EventSubscriberInterface
{
    public const IGNORED_ATTRIBUTES_CONTEXT = [
        'ignored_attributes' => [
            'password',
            'userIdentifier',
            '__initializer__',
            '__cloner__',
            '__isInitialized__',
        ],
    ];

    // Thanks to PHP 8's constructor property promotion and 8.1's readonly properties, we can
    // simply declare our class properties here in the constructor parameter list!
    public function __construct(
        private readonly EntityLoggerService $entityLogger,
        private readonly SerializerInterface $serializer,
        private $removals = [],
    ) {
    }

    // This function tells Symfony which Doctrine events we want to listen to.
    // The corresponding functions in this class will be called when these events are triggered.
    public static function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::preRemove,
            Events::postRemove,
        ];
    }

    /**
     * @throws Exception
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        $this->log($entity, EntityLoggerService::INSERT, $entityManager);
    }

    /**
     * @throws Exception
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        $this->log($entity, EntityLoggerService::UPDATE, $entityManager);
    }

    // We need to store the entity in a temporary array here, because the entity's ID is no longer
    // available in the postRemove event. We convert it to an array here, so we can retain the ID for
    // our audit log.
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->removals[] = $this->serializer->normalize($entity, null, self::IGNORED_ATTRIBUTES_CONTEXT);
    }

    /**
     * @throws Exception
     */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        $this->log($entity, EntityLoggerService::DELETE, $entityManager);
    }

    // This is the function which calls the EntityLoggerService service, constructing
    // the call to `EntityLoggerService::log()` with the appropriate parameters.
    /**
     * @throws Exception
     */
    private function log($entity, string $action, EntityManagerInterface $em): void
    {
        $entityClass = get_class($entity);
        // If the class is Log entity, ignore. We don't want to audit our own logs!
        if ('App\Entity\Log' === $entityClass) {
            return;
        }
        $entityId = $entity->getId();
        $entityType = str_replace('App\Entity\\', '', $entityClass);
        // The Doctrine unit of work keeps track of all changes made to entities.
        $uow = $em->getUnitOfWork();
        if ('delete' === $action) {
            // For deletions, we get our entity from the temporary array.
            $entityData = array_pop($this->removals);
            $entityId = $entityData['id'];
        } elseif ('insert' === $action) {
            // For insertions, we convert the entity to an array.
            $entityData = $this->serializer->normalize($entity, null, self::IGNORED_ATTRIBUTES_CONTEXT);
        } else {
            // For updates, we get the change set from Doctrine's Unit of Work manager.
            // This gives an array which contains only the fields which have
            // changed. We then just convert the numerical indexes to something
            // a bit more readable; "from" and "to" keys for the old and new values.
            $entityData = $uow->getEntityChangeSet($entity);
            foreach ($entityData as $field => $change) {
                $entityData[$field] = [
                    'from' => $change[0],
                    'to' => $change[1],
                ];
            }

            $collectionData = $uow->getScheduledCollectionUpdates();
            foreach ($collectionData as $key => $value) {
                if (is_object($value[0])) {
                    $diff = null;

                    if ('insert' === $action || 'update' === $action) {
                        $diff = $value->getInsertDiff();
                    } elseif ('delete' === $action) {
                        $diff = $value->getInsertDiff();
                    }

                    $data = $this->serializer->normalize($diff, null, self::IGNORED_ATTRIBUTES_CONTEXT);
                    $entityData[$entityType]['collectionData'] = $data;
                }

                // $value->getSnapshot()
                // $value->getDeleteDiff()
                // $value->getInsertDiff()

                //                if (is_object($value[0])) {
                //                    $entityData[$key][0] = $this->serializer->normalize($value[0], null, self::IGNORED_ATTRIBUTES_CONTEXT);
                //                }
                //                if (is_object($value[1])) {
                //                    $entityData[$key][1] = $this->serializer->normalize($value[1], null, self::IGNORED_ATTRIBUTES_CONTEXT);
                //                }
            }
        }

        $this->entityLogger->log($entityType, $entityId, $action, $entityData);
    }
}
