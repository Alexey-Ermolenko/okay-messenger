<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241208174823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user_friends_request table';
    }

    public function up(Schema $schema): void
    {
        // Create user_friends_request table
        $this->addSql('CREATE TABLE user_friends_request (
            id              INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, 
            requested_at    TIMESTAMP(0) WITHOUT TIME ZONE,
            responded_at    TIMESTAMP(0) WITHOUT TIME ZONE,
            user_id         INT NOT NULL,
            friend_id       INT NOT NULL,
            accepted        BOOLEAN NOT NULL,
            PRIMARY KEY(id)
        )');

        $this->addSql('CREATE INDEX IDX_USER_REQUEST_ID ON user_friends_request (user_id)');
        $this->addSql('CREATE INDEX IDX_FRIEND_REQUEST_ID ON user_friends_request (friend_id)');

        $this->addSql('ALTER TABLE user_friends_request 
            ADD CONSTRAINT IDX_USER_REQUEST_ID FOREIGN KEY (user_id) REFERENCES "user" (id) 
                NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('ALTER TABLE user_friends_request 
            ADD CONSTRAINT IDX_FRIEND_REQUEST_ID FOREIGN KEY (friend_id) REFERENCES "user" (id) 
                NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
    }

    public function down(Schema $schema): void
    {
        // Delete user_friends_request table
        $this->addSql('DROP TABLE user_friends_request');
    }
}
