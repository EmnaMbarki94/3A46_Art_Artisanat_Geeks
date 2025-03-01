<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301013825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation CHANGE desc_r desc_r LONGTEXT NOT NULL, CHANGE date_r date_r DATETIME NOT NULL');
        $this->addSql('ALTER TABLE reponse ADD rating INT DEFAULT NULL, CHANGE desc_rep desc_rep LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation CHANGE desc_r desc_r VARCHAR(255) NOT NULL, CHANGE date_r date_r DATE NOT NULL');
        $this->addSql('ALTER TABLE reponse DROP rating, CHANGE desc_rep desc_rep VARCHAR(255) NOT NULL');
    }
}
