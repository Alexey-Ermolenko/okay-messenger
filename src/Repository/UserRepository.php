<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Model\UserRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

/**
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
        private readonly ManagerRegistry $registry,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EventDispatcherInterface $eventDispatcher,
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

    public function update(int $userId, UserRequest $userRequest): User
    {
        $user = $this->find($userId);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        $user->setUsername($userRequest->getUsername());

        if ($userRequest->getPassword()) {
            $user->setPassword($this->hasher->hashPassword($user, $userRequest->getPassword()));
        }

        $user->setPhoneNumber($userRequest->getPhoneNumber());
        $user->setEmail($userRequest->getEmail());
        $user->setUsername($userRequest->getUsername());
        $user->setTelegramAccountLink($userRequest->getTelegramAccountLink());

        $user->setEventDispatcher($this->eventDispatcher);
        $user->setPreferredNotificationMethod($userRequest->getPreferredNotificationMethod());
        $this->saveAndCommit($user);

        return $user;
    }
}
