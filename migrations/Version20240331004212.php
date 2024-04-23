<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331004212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ok_notification (
            id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, 
            from_user_id INT NOT NULL,
            to_user_id INT NOT NULL,
            delivered BOOLEAN NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
            PRIMARY KEY(id)
        )');

        $this->addSql('CREATE INDEX IDX_FROM_USER_ID ON ok_notification (from_user_id)');
        $this->addSql('CREATE INDEX IDX_TO_USER_ID ON ok_notification (to_user_id)');
        $this->addSql('COMMENT ON COLUMN ok_notification.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE ok_notification 
            ADD CONSTRAINT IDX_FROM_USER_ID FOREIGN KEY (from_user_id) REFERENCES "user" (id) 
                NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('ALTER TABLE ok_notification 
            ADD CONSTRAINT IDX_TO_USER_ID FOREIGN KEY (to_user_id) REFERENCES "user" (id) 
                NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ok_notification');
    }
}