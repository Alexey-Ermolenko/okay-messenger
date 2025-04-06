<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303122147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add UNIQUE NULLABLE columns telegram_account_link and phone_number and add preferred_notification_method column';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD telegram_account_link VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD phone_number VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD preferred_notification_method VARCHAR(20) NOT NULL DEFAULT \'email\'');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_TELEGRAM_ACCOUNT_LINK ON "user" (telegram_account_link) WHERE telegram_account_link IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PHONE_NUMBER ON "user" (phone_number) WHERE phone_number IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_TELEGRAM_ACCOUNT_LINK ON users');
        $this->addSql('DROP INDEX UNIQ_PHONE_NUMBER ON users');
        $this->addSql('ALTER TABLE "user" DROP telegram_account_link');
        $this->addSql('ALTER TABLE "user" DROP phone_number');
        $this->addSql('ALTER TABLE "user" DROP preferred_notification_method');
    }
}
