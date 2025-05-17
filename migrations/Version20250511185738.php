<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250511185738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Company table and add company relation to User table';
    }

    public function up(Schema $schema): void
    {
        // Create Company table
        $this->addSql(<<<'SQL'
            CREATE TABLE company (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                address VARCHAR(255) DEFAULT NULL, 
                phone VARCHAR(100) DEFAULT NULL, 
                website VARCHAR(255) DEFAULT NULL
            )
        SQL);

        // Add company_id column to User table
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD COLUMN company_id INTEGER DEFAULT NULL REFERENCES company (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Remove company_id from user table
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE user_backup(id, email, roles, password);
            INSERT INTO user_backup SELECT id, email, roles, password FROM user;
            DROP TABLE user;
            CREATE TABLE user (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                email VARCHAR(180) NOT NULL,
                roles CLOB NOT NULL --(DC2Type:json)
                ,
                password VARCHAR(255) NOT NULL
            );
            INSERT INTO user SELECT id, email, roles, password FROM user_backup;
            DROP TABLE user_backup;
        SQL);

        // Drop Company table
        $this->addSql(<<<'SQL'
            DROP TABLE company
        SQL);
    }
}
