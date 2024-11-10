<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241103203830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set user_id nullable on logs table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE logs ALTER COLUMN user_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE logs ALTER COLUMN user_id SET NOT NULL');
    }
}
