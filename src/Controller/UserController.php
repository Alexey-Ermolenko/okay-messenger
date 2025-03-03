<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Entity\User;
use App\Enum\RequestStatus;
use App\Model\ErrorResponse;
use App\Model\UserRequest;
use App\Repository\UserRepository;
use App\Service\UserService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/v1/user', name: 'api_user')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,

        private readonly UserService $userService,
    ) {
    }

    #[Route(path: '/me', name: 'api_user_me', methods: [Request::METHOD_GET])]
    public function me(#[CurrentUser] UserInterface $user): JsonResponse
    {
        return $this->json($user);
    }

    #[Route(
        path: '/list',
        name: 'api_user_list',
        methods: [Request::METHOD_GET],
    )]
    public function list(): JsonResponse
    {
        $users = $this->userRepository->findAll();

        return $this->json($users);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/update/{id}', name: 'api_user_update', methods: [Request::METHOD_POST])]
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
     * @param UserInterface $user
     */
    #[Route(
        path: '/friend/send-request/{id}',
        name: 'api_user_friend_send_request',
        methods: [Request::METHOD_POST],
    )]
    public function sendFriendRequest(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        $result = $this->userService->sendFriendRequest($user, $id);

        return $this->json($result);
    }

    /**
     * @param UserInterface $user
     */
    #[Route(
        path: '/friend/delete/{id}',
        name: 'api_user_delete_friend',
        methods: [Request::METHOD_DELETE],
    )]
    public function deleteFriend(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        $result = $this->userService->deleteFriend($user, $id);

        return $this->json($result);
    }

    #[Route(
        path: '/friend/request/delete/user/{user_id}/friend/{friend_id}',
        name: 'api_user_delete_friend_request',
        methods: [Request::METHOD_DELETE],
    )]
    public function deleteUserFriendRequest(int $user_id, int $friend_id): JsonResponse
    {
        $result = $this->userService->deleteFriendRequest($user_id, $friend_id);

        return $this->json($result);
    }

    #[Route(
        path: '/friend/send/{id}',
        name: 'api_user_send_okay',
        methods: [Request::METHOD_POST],
    )]
    public function sendOkay(int $id, #[CurrentUser] UserInterface $user): JsonResponse
    {
        $resultMessage = $this->userService->sendOkay($id, $user);

        return new JsonResponse([
            'result' => RequestStatus::Success,
            'notification_message' => $resultMessage,
        ], Response::HTTP_OK);
    }
}
