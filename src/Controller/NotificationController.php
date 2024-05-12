<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/v1', name: 'api_index')]
class NotificationController extends AbstractController
{
    public const SUCCESS_BODY = ['result' => 'success'];

    public function __construct(
        private readonly NotificationRepository $notificationRepository,
    ) {
    }

    #[Route('/notification/list/my', name: 'notification_list_my', methods: [Request::METHOD_GET])]
    public function myList(#[CurrentUser] UserInterface $user): JsonResponse
    {
        $notifications = $this->notificationRepository->findBy([
            'to_user_id' => $user->getId(),
        ]);

        return $this->json([self::SUCCESS_BODY, $notifications]);
    }

    #[Route('/notification/list/sended', name: 'notification_list_sended', methods: [Request::METHOD_GET])]
    public function sendedList(#[CurrentUser] UserInterface $user): JsonResponse
    {
        $notifications = $this->notificationRepository->findBy([
            'from_user_id' => $user->getId(),
        ]);

        return $this->json([self::SUCCESS_BODY, $notifications]);
    }


    #[Route('/notification/get/{id}', name: 'notification_get', methods: [Request::METHOD_GET])]
    public function get(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        $notification = $this->notificationRepository->findOneBy([
            'id' => $id,
            'from_user_id' => $user->getId(),
        ]);

        return $this->json([self::SUCCESS_BODY, $notification]);
    }
}
