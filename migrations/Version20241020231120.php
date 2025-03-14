<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241020231120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create raw_logs table';
    }

    public function up(Schema $schema): void
    {
        // Create raw_logs table
        $this->addSql('CREATE TABLE raw_logs (
            id                  INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, 
            requested_at        TIMESTAMP(0) WITHOUT TIME ZONE       NOT NULL,
            responded_at        TIMESTAMP(0) WITHOUT TIME ZONE       NOT NULL,
            status              VARCHAR(255)                         NOT NULL,
            request_headers     JSON                                 NOT NULL,
            request_body        TEXT                                 NOT NULL,
            response_headers    JSON                                 NOT NULL,
            response_body       TEXT                                 NOT NULL,
            PRIMARY KEY(id)
        )');
    }

    public function down(Schema $schema): void
    {
        // Delete raw_logs table
        $this->addSql('DROP TABLE raw_logs');
    }
}
