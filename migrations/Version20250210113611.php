<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210113611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', ADD specialite VARCHAR(255) DEFAULT NULL, ADD point INT DEFAULT NULL, ADD cin VARCHAR(255) DEFAULT NULL, DROP role, DROP dtype, CHANGE email email VARCHAR(180) NOT NULL, CHANGE address address LONGTEXT NOT NULL, CHANGE verification_code verification_code VARCHAR(255) DEFAULT NULL, CHANGE is_verified is_verified TINYINT(1) DEFAULT NULL, CHANGE user_agent user_agent VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user ADD role VARCHAR(255) NOT NULL, ADD dtype VARCHAR(255) NOT NULL, DROP roles, DROP specialite, DROP point, DROP cin, CHANGE email email VARCHAR(255) NOT NULL, CHANGE address address VARCHAR(255) NOT NULL, CHANGE verification_code verification_code VARCHAR(255) NOT NULL, CHANGE is_verified is_verified TINYINT(1) NOT NULL, CHANGE user_agent user_agent VARCHAR(255) NOT NULL');
    }
}
