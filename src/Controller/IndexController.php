<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1', name: 'api_index')]
class IndexController extends AbstractController
{
    public const SUCCESS_BODY = ['result' => 'success'];

    #[Route('/healthcheck/ping', name: 'healthcheck_ping', methods: ["GET"])]
    public function ping(): JsonResponse
    {
        return new JsonResponse(self::SUCCESS_BODY, Response::HTTP_OK);
    }
}
