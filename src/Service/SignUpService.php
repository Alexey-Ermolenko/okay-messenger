<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Model\SignUpRequest;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class SignUpService
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private UserRepository $userRepository,
        private AuthenticationSuccessHandler $successHandler,
    ) {
    }

    public function signUp(SignUpRequest $signUpRequest): Response
    {
        if ($this->userRepository->existsByEmail($signUpRequest->getEmail())) {
            throw new UserAlreadyExistsException();
        }

        $user = (new User())
            // ->setRoles('["ROLE_USER", "ROLE_ADMIN"]')
            ->setRoles(['ROLE_USER'])
            ->setUsername($signUpRequest->getUserName())
            ->setEmail($signUpRequest->getEmail());

        $user->setPassword($this->hasher->hashPassword($user, $signUpRequest->getPassword()));

        $this->userRepository->saveAndCommit($user);

        return $this->successHandler->handleAuthenticationSuccess($user);
    }
}
