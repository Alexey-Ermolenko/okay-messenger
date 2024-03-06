<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    #[Route('/', name: 'app_index')]
    public function index(): JsonResponse
    {
        $sql = "SELECT * FROM test";
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
            'path' => 'src/Controller/IndexController.php',
            'test_data' => $formattedTestData,
        ]);
    }
}
