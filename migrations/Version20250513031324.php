<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513031324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add fullName column to User table';
    }

    public function up(Schema $schema): void
    {
        // Add fullName column to User table
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD COLUMN full_name VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Remove fullName column from User table
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP COLUMN full_name
        SQL);
    }
}
