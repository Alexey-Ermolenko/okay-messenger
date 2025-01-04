<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250101000458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set entity_id field as nullable in logs table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE logs ALTER COLUMN entity_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE logs ALTER COLUMN entity_id SET NOT NULL');
    }
}
