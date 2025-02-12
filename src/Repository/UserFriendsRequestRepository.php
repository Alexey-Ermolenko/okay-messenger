<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserFriendsRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<UserFriendsRequest>
 *
 * @method UserFriendsRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFriendsRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFriendsRequest[]    findAll()
 * @method UserFriendsRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFriendsRequestRepository extends ServiceEntityRepository
{
    use RepositoryModifyTrait;

    public string $_entityName;
    public mixed $_em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFriendsRequest::class);
        $this->_entityName = UserFriendsRequest::class;
        $this->_em = $this->getEntityManager();
    }

    public function getUserFriendsRequest(int $id): UserFriendsRequest
    {
        $userFriendsRequest = $this->find($id);
        if (null === $userFriendsRequest) {
            throw new NotFoundHttpException();
        }

        return $userFriendsRequest;
    }

    //    /**
    //     * @return UserFriendsRequest[] Returns an array of UserFriendsRequest objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserFriendsRequest
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
