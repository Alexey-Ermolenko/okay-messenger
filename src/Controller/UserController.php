<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Entity\Notification;
use App\Entity\User;
use App\Entity\UserFriendsRequest;
use App\Enum\RequestStatus;
use App\Message\EmailMessage;
use App\Message\UserFriendRequestMessage;
use App\Model\ErrorResponse;
use App\Model\UserRequest;
use App\Repository\NotificationRepository;
use App\Repository\UserFriendsRequestRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

#[Route('/api/v1/user', name: 'api_user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly NotificationRepository $notificationRepository,
        private readonly UserFriendsRequestRepository $userFriendsRequestRepository,
        private readonly MessageBusInterface $messageBus,
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

    /**
     * @throws \Exception
     */
    #[Route('/update/{id}', name: 'api_user_update', methods: [Request::METHOD_POST])]
    #[OA\Response(response: 200, description: 'Updates a user', attachables: [new Model(type: User::class)])]
    #[OA\Response(response: 409, description: 'User not exists', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: UserRequest::class)])]
    public function update(int $id, #[RequestBody] UserRequest $userRequest): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException(message: 'User not found');
        }

        $resultUser = $this->userRepository->update($id, $userRequest);

        return $this->json([
            'result' => RequestStatus::Success,
            'user' => $resultUser,
        ]);
    }

    /**
     * @param int $id
     * @param UserInterface $user
     * @return JsonResponse
     */
    #[Route('/friend/send-request/{id}', name: 'api_user_friend_send_request', methods: [Request::METHOD_POST])]
    public function sendFriendRequest(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        /**
            $user = $this->userRepository->find($user->getId());
            $friend = $this->userRepository->getUser($id);

            $resultUser = $user->addFriend($friend);
            $this->userRepository->saveAndCommit($resultUser);

            return $this->json([
                'result' => RequestStatus::Success,
                'user' => $resultUser,
            ]);
        */

        /** @var User $user */
        $user = $this->userRepository->find($user->getId());
        $friend = $this->userRepository->getUser($id);

        $userFriendsRequest = new UserFriendsRequest();

        $userFriendsRequest->setUserId($user->getId());
        $userFriendsRequest->setFriendId($friend->getId());

        $this->userFriendsRequestRepository->saveAndCommit($userFriendsRequest);


        $msg = (string) json_encode([
            'user' => [
                'email' => $user->getEmail(),
                'id' => $user->getId(),
            ],
            'toFriend' => [
                'email' => $friend->getEmail(),
                'id' => $friend->getId(),
            ],
        ]);

        $this->messageBus->dispatch(
            message: new UserFriendRequestMessage($msg)
        );

        return $this->json([
            'result' => RequestStatus::Success,
            'notification' => 'Adding friend request was sent successfully',
        ]);
    }

    /**
     * @param int $id
     * @param UserInterface $user
     * @return JsonResponse
     */
    #[Route('/friend/delete/{id}', name: 'api_user_delete_friend', methods: [Request::METHOD_DELETE])]
    public function deleteFriend(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        /** @var User $user */
        $user = $this->userRepository->find($user->getId());

        $deletedFriend = $this->userRepository->getUser($id);

        $user = $user->removeFriend($deletedFriend);
        $this->userRepository->saveAndCommit($user);

        return $this->json([
            'result' => RequestStatus::Success,
            'user' => $user,
        ]);
    }

    #[Route('/friend/accept/{id}', name: 'api_user_accept_friend', methods: [Request::METHOD_POST])]
    public function acceptFriend(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        /** @var User $user */
        $user = $this->userRepository->find($user->getId());
        $friend = $this->userRepository->getUser($id);

        $resultUser = $user->addFriend($friend);
        $this->userRepository->saveAndCommit($resultUser);

        return $this->json([
            'result' => RequestStatus::Success,
            'user' => $resultUser,
        ]);
    }

    #[Route('/friend/send/{id}', name: 'api_user_send_okay', methods: [Request::METHOD_POST])]
    public function sendOkay(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        // TODO: add ok_notification record
        //  https://ru.linux-console.net/?p=7773&ysclid=lvqe6sikvp803026962
        //  https://www.youtube.com/watch?v=uc6ev8d6j_M
        /**
         * INSERT INTO public.ok_notification (from_user_id, to_user_id, delivered, created_at)
         * VALUES (1, 6, true, '2024-04-23 22:56:18');.
         */
        // TODO: send push notify   to user by userId
        // TODO: send email message to user by email

        $userToSend = $this->userRepository->getUser($id);

        /** @var User $user */
        $msg = (string) json_encode([
            'fromEmail' => $user->getEmail(),
            'toEmail' => $userToSend->getEmail(),
        ]);

        $this->messageBus->dispatch(
            message: new EmailMessage($msg)
        );

        $notification = new Notification($user->getId(), $id);
        $notification->setDelivered(true);

        $this->notificationRepository->saveAndCommit($notification);

        return $this->json([
            'result' => RequestStatus::Success,
            'notification' => 'test',
        ]);
    }
}
