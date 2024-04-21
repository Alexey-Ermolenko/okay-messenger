<?php

declare(strict_types=1);

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/logs', name: 'api_logs')]
final class LogsController extends AbstractController
{
    /**
     * Giving logs list by user
     * @SWG\Tag (name="Logs")
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
}
