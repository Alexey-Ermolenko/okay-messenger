<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\RequestStatus;
use App\Service\LogService;
use Doctrine\DBAL\Exception;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/v1/logs', name: 'api_logs')]
final class LogsController extends AbstractController
{
    public function __construct(
        private readonly LogService $logService,
    ) {
    }

    /**
     * Giving logs list by user.
     *
     * @SWG\Tag (name="api_logs_users")
     *
     * @SWG\Response (
     *     response=200,
     *     description="Giving logs list by user"
     * )
     *
     * @throws Exception
     */
    #[Route('/user/{id}', name: 'api_logs_users', methods: [Request::METHOD_GET])]
    public function getUserLogsById(string $id): JsonResponse
    {
        return $this->json([
            'result' => RequestStatus::Success,
            'logs' => $this->logService->findUserLogsByUserId($id),
        ]);
    }

    /**
     * Giving logs list by user.
     *
     * @SWG\Tag (name="api_logs_auth_users")
     *
     * @SWG\Response (
     *     response=200,
     *     description="Giving logs list by auth user"
     * )
     *
     * @throws Exception
     */
    #[Route('/user/my', name: 'api_logs_auth_users', methods: [Request::METHOD_GET])]
    public function getMyUserLogs(#[CurrentUser] UserInterface $user): JsonResponse
    {
        return $this->json([
            'result' => RequestStatus::Success,
            'logs' => $this->logService->findUserLogsByUserId((string) $user->getId()),
        ]);
    }

    /**
     * Giving logs list by notifications.
     *
     * @SWG\Tag (name="notifications_logs")
     *
     * @SWG\Response (
     *     response=200,
     *     description="Giving logs list by notifications"
     * )
     *
     * @throws Exception
     */
    #[Route('/user/notifications/my', name: 'api_logs_notifications', methods: [Request::METHOD_GET])]
    public function getNotificationLogs(#[CurrentUser] UserInterface $user): JsonResponse
    {
        return $this->json([
            'result' => RequestStatus::Success,
            'logs' => $this->logService->findNotificationLogsByUserId((string) $user->getId()),
        ]);
    }

    /**
     * Giving logs list by request.
     *
     * @SWG\Tag (name="requests_logs")
     *
     * @SWG\Response (response=200, description="Giving logs list by notification")
     *
     * @throws Exception
     */
    #[Route(
        path: '/requests/{since}',
        name: 'requests',
        defaults: ['since' => '2024-01-01T00:00:00'],
        methods: [Request::METHOD_GET],
    )]
    public function getRequestsLogs(#[CurrentUser] UserInterface $user, string $since): JsonResponse
    {
        $sinceDatetime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $since)
            ?: DateTimeImmutable::createFromFormat('Y-m-d', $since)
            ?: null;

        return $this->json([
            'result' => RequestStatus::Success,
            'logs' => $this->logService->findRequestsLogs($sinceDatetime),
        ]);
    }
}
