<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    use RepositoryModifyTrait;

    public string $_entityName;
    public mixed $_em;

    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Notification::class);
        $this->_entityName = Notification::class;
        $this->_em = $this->getEntityManager();
    }

    public function getNotification(int $notificationId): Notification
    {
        $notification = $this->find($notificationId);
        if (null === $notification) {
            throw new EntityNotFoundException();
        }

        return $notification;
    }
}
