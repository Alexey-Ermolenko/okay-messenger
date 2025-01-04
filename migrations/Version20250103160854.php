<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103160854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set accepted field as enums of string values';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_friends_request ALTER COLUMN accepted TYPE VARCHAR(8)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_friends_request ALTER COLUMN accepted TYPE BOOLEAN USING accepted::BOOLEAN');
    }
}
