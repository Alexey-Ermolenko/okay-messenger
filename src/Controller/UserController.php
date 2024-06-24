<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Notification;
use App\Enum\RequestStatus;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Service\EmailNotificatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/v1/user', name: 'api_user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly NotificationRepository $notificationRepository,
        private readonly EmailNotificatorService $notificator,
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
    public function addFriend(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        $user = $this->userRepository->find($user->getId());
        $friend = $this->userRepository->getUser($id);

        $resultUser = $user->addFriend($friend);
        $this->userRepository->saveAndCommit($resultUser);

        return $this->json([
            'result' => RequestStatus::Success,
            'user' => $resultUser
        ]);
    }

    #[Route('/friend/delete/{id}', name: 'api_user_delete_friend', methods: [Request::METHOD_DELETE])]
    public function deleteFriend(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        $user = $this->userRepository->find($user->getId());

        $deletedFriend = $this->userRepository->getUser($id);

        $user = $user->removeFriend($deletedFriend);
        $this->userRepository->saveAndCommit($user);

        return $this->json([
            'result' => RequestStatus::Success,
            'user' => $user
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/friend/send/{id}', name: 'api_user_send_okay', methods: [Request::METHOD_POST])]
    public function sendOkay(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        //TODO: add ok_notification record
        //  https://ru.linux-console.net/?p=7773&ysclid=lvqe6sikvp803026962
        //  https://www.youtube.com/watch?v=uc6ev8d6j_M
        /**
            INSERT INTO public.ok_notification (from_user_id, to_user_id, delivered, created_at)
            VALUES (1, 6, true, '2024-04-23 22:56:18');
         */
        //TODO: send push notify   to user by userId
        //TODO: send email message to user by email

        $userToSend = $this->userRepository->getUser($id);
        $email = $userToSend->getEmail();

        $this->notificator->sendEmail($user->getEmail(), $email);

        $notification = new Notification($user->getId(), $id);
        $notification->setDelivered(true);

        $this->notificationRepository->saveAndCommit($notification);

        return $this->json([
            'result' => RequestStatus::Success,
            'notification' => 'test'
        ]);
    }
}
