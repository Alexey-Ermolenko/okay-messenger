<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserFriendsRequest;
use App\Enum\RequestFriendRequestStatus;
use App\Enum\RequestStatus;
use App\Message\UserFriendRequestMessage;
use App\Repository\UserFriendsRequestRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserFriendsRequestRepository $userFriendsRequestRepository,
        private UrlGeneratorInterface $urlGenerator,
        private MessageBusInterface $messageBus
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

            $acceptFriendUrl = $this->urlGenerator->generate(
                'api_accept_user_friend',
                ['user_id' => $user->getId(), 'friend_id' => $friend->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $msg = json_encode([
                'msg' => UserFriendsRequest::class,
                'acceptFriendUrl' => $acceptFriendUrl,
                'user' => ['email' => $user->getEmail(), 'id' => $user->getId()],
                'toFriend' => ['email' => $friend->getEmail(), 'id' => $friend->getId()],
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
                'notification' => 'Authenticated user not found'
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
}
