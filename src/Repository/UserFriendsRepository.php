<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserFriends;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserFriends>
 *
 * @method UserFriends|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFriends|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFriends[]    findAll()
 * @method UserFriends[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFriendsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFriends::class);
    }

    //    /**
    //     * @return UserFriends[] Returns an array of UserFriends objects
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

    //    public function findOneBySomeField($value): ?UserFriends
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
