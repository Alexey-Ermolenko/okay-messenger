<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102151123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set user_id friend_id unique index';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX USER_FRIEND_UNIQUE_IDX ON user_friends_request (user_id, friend_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX USER_FRIEND_UNIQUE_IDX ON user_friends_request');
    }
}
