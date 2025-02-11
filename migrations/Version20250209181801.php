<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209181801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD image_path VARCHAR(255) DEFAULT NULL, DROP ensemble_photos, CHANGE desc_a desc_a LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE magasin CHANGE photo_m photo_m VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD nb_place INT NOT NULL, DROP prix_e');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD ensemble_photos LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP image_path, CHANGE desc_a desc_a VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE magasin CHANGE photo_m photo_m VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD prix_e DOUBLE PRECISION NOT NULL, DROP nb_place');
    }
}
