<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use Random\RandomException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seeds-user-friends-table',
    description: 'Command for filling user friends database table',
)]
class SeedsUserFriendsTableDatabaseCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $faker = Factory::create();

        $users = $this->userRepository->findAll();
        $userIds = array_map(fn ($item) => $item->getId(), $users);

        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection();

        $placeholders = [];
        $values = [];

        foreach ($userIds as $userId) {
            $friendCount = random_int(3, 10);
            $friendIds = $faker->randomElements(array_diff($userIds, [$userId]), $friendCount); // Исключаем самого себя

            foreach ($friendIds as $friendId) {
                $placeholders[] = '(?, ?)';
                $values[] = $userId;
                $values[] = $friendId;
            }
        }

        if (!empty($values)) {
            $connection->executeStatement(
                sprintf(
                    'INSERT INTO "user_friends" (user_id, friend_id) VALUES %s',
                    implode(', ', $placeholders),
                ),
                $values,
            );
        }

        $io->success('Successfully seeded 150 users!');

        return Command::SUCCESS;
    }
}
