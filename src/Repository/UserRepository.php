<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

/**
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    use RepositoryModifyTrait;

    public string $_entityName;
    public mixed $_em;

    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, User::class);
        $this->_entityName = User::class;
        $this->_em = $this->getEntityManager();
    }

    public function existsByEmail(string $email): bool
    {
        return null !== $this->findOneBy(['email' => $email]);
    }

    public function getUser(int $userId): User
    {
        $user = $this->find($userId);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /** @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteFriend(int $id): void
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection();
        $connection->delete('user_friends', ['friend_id' => $id]);
    }

    /** @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function addFriend(int $id): void
    {
        $data = [
            'user_id' => $rule['payment_method_id'] ?? null,
            'friend_id' => $rule['payment_type'] ?? null,
        ];

        /** @var Connection $connection */
        $connection = $this->registry->getConnection();
        $connection->insert('user_friends', $data);
    }
}
