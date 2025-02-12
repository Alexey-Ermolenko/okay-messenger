<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\RequestFriendRequestStatus;
use App\Repository\UserFriendsRequestRepository;
use App\Repository\UserRepository;
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
        private readonly UserRepository $userRepository,
        private readonly UserFriendsRequestRepository $userFriendsRequestRepository,
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
        // TODO:
        //  check friend_request by friend_id and user_id
        //  if friend_request by friend_id and user_id is exists and accepted == false
        //  ...
        //  then addFriend and friend_request set accepted = true
        //  else throw error message
        //  ...
        //  http://localhost:8080/api/v1/user/friend/accept/user/9/friend/11
        //  ...
        //  INSERT INTO public.user_friends_request
        //  (id, requested_at, responded_at, user_id, friend_id, accepted)
        //  VALUES
        //  (44, '2025-01-02 19:41:29', null, 11, 9, false);

        /** @var User $user */
        $user = $this->userRepository->find($user_id);
        /** @var User $friend */
        $friend = $this->userRepository->find($friend_id);

        if ($user && $friend) {
            // TODO: найти userFriendsRequestRepository которые равны pending
            $userFriendsRequestRepository = $this->userFriendsRequestRepository->findOneBy([
                'user_id' => $user_id,
                'friend_id' => $friend_id,
                'accepted' => [
                    RequestFriendRequestStatus::pending->value,
                    RequestFriendRequestStatus::deleted->value,
                ],
            ]);

            if ($userFriendsRequestRepository) {
                $resultUser = $user->addFriend($friend);
                $userFriendsRequestRepository->setAccepted(RequestFriendRequestStatus::accepted->value);

                $this->userFriendsRequestRepository->saveAndCommit($userFriendsRequestRepository);
                $this->userRepository->saveAndCommit($resultUser);

                return new Response(
                    '<html><body>Friend '.$user->getUsername().' added</body></html>'
                );
            }
        }

        return new Response(
            '<html><body>Failed</body></html>'
        );
    }
}
