<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Entity\UserFriendsRequest;
use App\Enum\NotificationPreference;
use App\Enum\RequestFriendRequestStatus;
use App\Enum\RequestStatus;
use App\Message\EmailMessage;
use App\Message\TelegramMessage;
use App\Message\UserFriendRequestMessage;
use App\Repository\NotificationRepository;
use App\Repository\UserFriendsRequestRepository;
use App\Repository\UserRepository;
use App\Service\NotificationStrategy\NotificationStrategy;
use App\Service\NotificationStrategy\Strategy\EmailNotificationStrategy;
use App\Service\NotificationStrategy\Strategy\TelegramNotificationStrategy;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserFriendsRequestRepository $userFriendsRequestRepository,
        private UrlGeneratorInterface $urlGenerator,
        private MessageBusInterface $messageBus,
        private NotificationRepository $notificationRepository,
    ) {
    }

    private function getAuthenticatedUser(UserInterface $user): ?User
    {
        return $this->userRepository->find($user->getId());
    }

    public function sendFriendRequest(UserInterface $user, int $friendId): array
    {
        /**
         * $user = $this->userRepository->find($user->getId());
         * $friend = $this->userRepository->getUser($id);.
         *
         * $resultUser = $user->addFriend($friend);
         * $this->userRepository->saveAndCommit($resultUser);
         *
         * return $this->json([
         * 'result' => RequestStatus::Success,
         * 'user' => $resultUser,
         * ]);
         */

        /** @var User $user */
        $user = $this->getAuthenticatedUser($user);
        if (!$user) {
            return ['result' => RequestStatus::Error, 'notification' => 'Authenticated user not found'];
        }

        $friend = $this->userRepository->getUser($friendId);

        if ($user->myFriends->contains($friend)) {
            return [
                'result' => RequestStatus::Error,
                'notification' => 'This user is already a friend',
            ];
        }

        $userFriendsRequest = new UserFriendsRequest();
        $userFriendsRequest->user = $user;
        $userFriendsRequest->friend = $friend;
        $userFriendsRequest->setAccepted(RequestFriendRequestStatus::pending->value);

        try {
            $this->userFriendsRequestRepository->saveAndCommit($userFriendsRequest);

            $acceptFriendUrl = $this->urlGenerator->generate('api_accept_user_friend',
                [
                    'user_id' => $user->getId(),
                    'friend_id' => $friend->getId(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $msg = json_encode([
                'msg' => UserFriendsRequest::class,
                'acceptFriendUrl' => $acceptFriendUrl,
                'user' => [
                    'email' => $user->getEmail(),
                    'id' => $user->getId(),
                ],
                'toFriend' => [
                    'email' => $friend->getEmail(),
                    'id' => $friend->getId(),
                ],
            ]);

            $this->messageBus->dispatch(new UserFriendRequestMessage($msg));

            return [
                'result' => RequestStatus::Success,
                'notification' => 'Friend request sent successfully',
            ];
        } catch (\Exception $exception) {
            return [
                'result' => RequestStatus::Error,
                'notification' => $exception->getMessage(),
            ];
        }
    }

    public function deleteFriendRequest(int $userId, int $friendId): array
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);
        /** @var User $friend */
        $friend = $this->userRepository->find($friendId);

        if (!$user || !$friend) {
            return [
                'result' => RequestStatus::Error,
                'notification' => 'User or friend not found',
            ];
        }

        $friendRequest = $this->userFriendsRequestRepository->findOneBy([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'accepted' => RequestFriendRequestStatus::pending->value,
        ]);

        if (!$friendRequest) {
            return [
                'result' => RequestStatus::Error,
                'notification' => 'Friend request not found',
            ];
        }

        $this->userFriendsRequestRepository->removeAndCommit($friendRequest);

        return [
            'result' => RequestStatus::Success,
            'user_id' => $userId,
            'friend_id' => $friendId,
        ];
    }

    public function deleteFriend(UserInterface $user, int $friendId): array
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser($user);

        if (!$user) {
            return [
                'result' => RequestStatus::Error,
                'notification' => 'Authenticated user not found',
            ];
        }

        $deletedFriend = $this->userRepository->getUser($friendId);

        if (!$user->myFriends->contains($deletedFriend)) {
            return [
                'result' => RequestStatus::Error,
                'notification' => 'This user is not your friend',
            ];
        }

        $user->removeFriend($deletedFriend);
        $this->userRepository->saveAndCommit($user);

        return [
            'result' => RequestStatus::Success,
            'notification' => 'Friend removed successfully',
        ];
    }

    public function acceptFriendRequest(int $userId, int $friendId): array
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);
        /** @var User $friend */
        $friend = $this->userRepository->find($friendId);

        if (!$user || !$friend) {
            return [
                'result' => RequestStatus::Error,
                'notification' => 'User or friend not found',
            ];
        }

        $friendRequest = $this->userFriendsRequestRepository->findOneBy([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'accepted' => [
                RequestFriendRequestStatus::pending->value,
                RequestFriendRequestStatus::deleted->value,
            ],
        ]);

        if (!$friendRequest) {
            return [
                'result' => RequestStatus::Error,
                'notification' => 'Friend request not found or already processed',
            ];
        }

        $user->addFriend($friend);
        $friendRequest->setAccepted(RequestFriendRequestStatus::accepted->value);

        $this->userFriendsRequestRepository->saveAndCommit($friendRequest);
        $this->userRepository->saveAndCommit($user);

        return [
            'result' => RequestStatus::Success,
            'notification' => "Friend {$user->getUsername()} added",
        ];
    }

    public function sendOkay(int $recipientId, UserInterface $sender): string
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

        $userToSend = $this->userRepository->getUser($recipientId);

	    $strategy = match ($userToSend->getPreferredNotificationMethod()) {
		    NotificationPreference::Email->value => new EmailNotificationStrategy($this->messageBus),
		    NotificationPreference::Telegram->value => new TelegramNotificationStrategy($this->messageBus),
		    default => throw new \InvalidArgumentException('Unsupported notification method'),
	    };

	    $notificationContext = new NotificationStrategy($strategy);

	    /** @var User $sender */
	    $msg = $notificationContext->execute($sender, $userToSend);

	    $notification = new Notification($sender->getId(), $recipientId);
	    $notification->setDelivered(true);
	    $this->notificationRepository->saveAndCommit($notification);

	    return $msg;
    }
}
