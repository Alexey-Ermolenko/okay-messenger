<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1', name: 'api_v1')]
class DashboardController extends AbstractController
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $sql = 'SELECT * FROM test';
        $testData = $this->connection->fetchAllAssociative($sql);

        $formattedTestData = [];
        foreach ($testData as $testItem) {
            $formattedTestData[] = [
                'id' => $testItem['id'],
                'name' => $testItem['name'],
            ];
        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DashboardController.php',
            'test_data' => $formattedTestData,
        ]);
    }
}
