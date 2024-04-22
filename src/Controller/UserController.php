<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/v1/user', name: 'api_user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route('/me', name: 'api_user_me', methods: [Request::METHOD_GET])]
    public function me(#[CurrentUser] UserInterface $user): JsonResponse
    {
        return $this->json($user);
    }

    #[Route('/list', name: 'api_user_list', methods: [Request::METHOD_GET])]
    public function list(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        return $this->json($users);
    }

    #[Route('/friend/add/{id}', name: 'api_user_add_friend', methods: [Request::METHOD_POST])]
    public function addFriend(string $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        $user = $this->userRepository->find($user->getId());

        $resultUser = $user->addFriend($this->userRepository->find($id));
        $this->userRepository->saveAndCommit($resultUser);

        return $this->json([
            'result' => 'ok',
            'user' => $resultUser
        ]);
    }

    #[Route('/friend/delete/{id}', name: 'api_user_delete_friend', methods: [Request::METHOD_POST])]
    public function deleteFriend(string $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        $user = $this->userRepository->find($user->getId());

        $deletedUser = $user->removeFriend($this->userRepository->find($id));
        $this->userRepository->removeAndCommit($deletedUser);

        return $this->json([
            'result' => 'ok',
            'user' => $user
        ]);
    }

    #[Route('/okay/send/{id}', name: 'api_user_send_okay', methods: [Request::METHOD_POST])]
    public function sendOkay(string $id): JsonResponse
    {
        //TODO:

        return $this->json([
        ]);
    }
}
