<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521232822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'update logs table';
    }

    public function up(Schema $schema): void
    {
        //delete logs table
        $this->addSql('DROP INDEX IDX_LOGS_LEVEL_CHANNEL_DATETIME ON logs');
        $this->addSql('DROP TABLE logs');

        //add new logs table
        $this->addSql('CREATE TABLE logs (
            id          INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, 
            entity_type VARCHAR(255)                         NOT NULL,
            entity_id   INT                                  NOT NULL,
            action      VARCHAR(255)                         NOT NULL,
            data        JSON                                 NOT NULL,
            created_at  TIMESTAMP(0) WITHOUT TIME ZONE       NOT NULL,
            user_id     INT                                  NOT NULL,
            ip_address  VARCHAR(64)                          NOT NULL,
            route       VARCHAR(128)                         NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN logs.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX IDX_LOGS_ENTITY_ID_USER_ID ON logs (entity_id, user_id)');


    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE logs');
        $this->addSql('CREATE TABLE logs (
            id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, 
            level VARCHAR(255) NOT NULL,
            channel VARCHAR(255) NOT NULL,
            datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            message VARCHAR(512) NOT NULL,
            context JSON NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN logs.datetime IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX IDX_LOGS_LEVEL_CHANNEL_DATETIME ON logs (level, channel, datetime)');
    }
}