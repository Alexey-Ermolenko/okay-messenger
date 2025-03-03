<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\NotificationPreference;
use App\Enum\RequestStatus;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/api/v1',
    name: 'api_index',
)]
final class IndexController extends AbstractController
{
    public const SUCCESS_BODY = ['result' => 'success'];

    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    #[Route(
        path: '/healthcheck/ping',
        name: 'healthcheck_ping',
        methods: [Request::METHOD_GET],
    )]
    public function ping(): JsonResponse
    {
        return new JsonResponse(self::SUCCESS_BODY, Response::HTTP_OK);
    }

    #[Route(
        path: '/accept/user/{user_id}/friend/{friend_id}',
        name: 'api_accept_user_friend',
        methods: [Request::METHOD_GET],
    )]
    public function acceptFriend(int $user_id, int $friend_id): JsonResponse
    {
        $result = $this->userService->acceptFriendRequest($user_id, $friend_id);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    #[Route(
        path: '/notification-preferences-list/get',
        name: 'api_accept_user_friend',
        methods: [Request::METHOD_GET],
    )]
    public function getNotificationPreferencesList(): JsonResponse
    {
        return new JsonResponse([
            'result' => RequestStatus::Success,
            'notification_preferences' => NotificationPreference::cases(),
        ],
            Response::HTTP_OK,
        );
    }
}
