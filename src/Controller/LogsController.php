<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LogService;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/logs', name: 'api_logs')]
final class LogsController extends AbstractController
{
    public function __construct(
        private readonly LogService $logService,
    ) {
    }

    /**
     * Giving logs list by user
     * @SWG\Tag (name="user_logs")
     * @SWG\Response (
     *     response=200,
     *     description="Giving logs list by user"
     * )
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/user/{id}', name: 'api_logs_users', methods: [Request::METHOD_GET])]
    public function getUserLogs(string $id): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/LogsController.php',
        ]);
    }

    /**
     * Giving logs list by notification
     * @SWG\Tag (name="notifications_logs")
     * @SWG\Response (
     *     response=200,
     *     description="Giving logs list by notification"
     * )
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/notification/{id}', name: 'api_logs_notifications', methods: [Request::METHOD_GET])]
    public function getNotificationLogs(string $id): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/LogsController.php',
        ]);
    }

    /**
     * Giving logs list by request
     * @SWG\Tag (name="requests_logs")
     * @SWG\Response (
     *     response=200,
     *     description="Giving logs list by notification"
     * )
     * @param string $id
     * @return JsonResponse
     */
    #[Route(
        path: '/requests/{since}',
        name: 'requests',
        defaults: ['since' => ''],
        methods: [Request::METHOD_GET],
    )]
    public function getRequestsLogs(string $since): JsonResponse
    {
        $sinceDatetime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $since)
            ?: DateTimeImmutable::createFromFormat('Y-m-d', $since)
                ?: null;

        return $this->json($this->logService->findRequestsLogs($sinceDatetime));
    }

//    /**
//     * @param string $id search logs by paymentId|internalId|externalId
//     * @return JsonResponse
//     * @throws ExceptionDBAL
//     */
//    #[Route('/{id}', name: '', methods: [Request::METHOD_GET])]
//    public function getRawLogs(string $id): JsonResponse
//    {
//        return $this->responseFactory->json($this->rawLogService->getPaymentLogs($id));
//    }
}
