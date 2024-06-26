<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;

class RoleService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function grantAdmin(int $userId): void
    {
        $this->grantRole($userId, 'ROLE_ADMIN');
    }

    public function grantAuthor(int $userId): void
    {
        $this->grantRole($userId, 'ROLE_AUTHOR');
    }

    public function grantUser(int $userId): void
    {
        $this->grantRole($userId, 'ROLE_USER');
    }

    private function grantRole(int $userId, string $role): void
    {
        $user = $this->userRepository->getUser($userId);
        $user->setRoles([$role]);

        $this->userRepository->commit();
    }
}
