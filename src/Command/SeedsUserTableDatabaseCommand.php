<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Enum\NotificationPreference;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seeds-user-table',
    description: 'Command for filling user database table',
)]
class SeedsUserTableDatabaseCommand extends Command
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly ManagerRegistry $managerRegistry,
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

        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection();

        $placeholders = [];
        $values = [];
        for ($i = 0; $i < 150; ++$i) {
            $user = new User();

            $values[] = $faker->userName();
            $values[] = $faker->unique()->safeEmail();
            $values[] = '["ROLE_USER"]';
            $values[] = $this->hasher->hashPassword($user, 'password');
            $values[] = 'https://t.me/'.$faker->userName();
            $values[] = $faker->unique()->phoneNumber();
            $values[] = $faker->randomElement(NotificationPreference::cases())->value;

            $placeholders[] = '?, ?, ?, ?, ?, ?, ?';
        }

        $columns = [
            'username',
            'email',
            'roles',
            'password',
            'telegram_account_link',
            'phone_number',
            'preferred_notification_method',
        ];

        /* @noinspection SqlInsertValues */
        $connection->executeStatement(
            sprintf(
                'INSERT INTO "user" (%s) VALUES (%s)',
                implode(', ', $columns),
                implode('), (', $placeholders),
            ),
            $values,
        );

        $io->success('Successfully seeded 150 users!');

        return Command::SUCCESS;
    }
}
