<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513121542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create ZohoCRM table for integration';
    }

    public function up(Schema $schema): void
    {
        // Create zoho_crm table
        $this->addSql(<<<'SQL'
            CREATE TABLE zoho_crm (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                company_id INTEGER NOT NULL REFERENCES company (id),
                api_token VARCHAR(255) NOT NULL,
                refresh_token VARCHAR(255) NOT NULL,
                client_id VARCHAR(255) NOT NULL,
                client_secret VARCHAR(255) NOT NULL,
                is_active BOOLEAN NOT NULL,
                last_synced DATETIME DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                CONSTRAINT FK_ZOHO_CRM_COMPANY UNIQUE (company_id)
            )
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Drop zoho_crm table
        $this->addSql('DROP TABLE zoho_crm');
    }
}
