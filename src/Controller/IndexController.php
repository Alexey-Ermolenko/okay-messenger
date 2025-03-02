<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\RequestFriendRequestStatus;
use App\Repository\UserFriendsRequestRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1', name: 'api_index')]
final class IndexController extends AbstractController
{
    public const SUCCESS_BODY = ['result' => 'success'];

    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    #[Route('/healthcheck/ping', name: 'healthcheck_ping', methods: [Request::METHOD_GET])]
    public function ping(): JsonResponse
    {
        return new JsonResponse(self::SUCCESS_BODY, Response::HTTP_OK);
    }

    #[Route('/accept/user/{user_id}/friend/{friend_id}', name: 'api_accept_user_friend', methods: [Request::METHOD_GET])]
    public function acceptFriend(int $user_id, int $friend_id): Response
    {
        $result = $this->userService->acceptFriendRequest($user_id, $friend_id);
        return $this->json($result);
    }
}
