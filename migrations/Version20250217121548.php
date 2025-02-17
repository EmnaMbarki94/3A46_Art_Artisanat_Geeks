<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217121548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE image_path image_path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE commande ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('ALTER TABLE event ADD prix_s INT NOT NULL, ADD prix_vip INT NOT NULL, ADD nb_ticket INT NOT NULL, CHANGE date_e date_e DATETIME NOT NULL');
        $this->addSql('ALTER TABLE galerie ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE galerie ADD CONSTRAINT FK_9E7D1590A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9E7D1590A76ED395 ON galerie (user_id)');
        $this->addSql('ALTER TABLE reservation ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849559D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_42C849559D86650F ON reservation (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE image_path image_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('DROP INDEX IDX_6EEAA67DA76ED395 ON commande');
        $this->addSql('ALTER TABLE commande DROP user_id');
        $this->addSql('ALTER TABLE event DROP prix_s, DROP prix_vip, DROP nb_ticket, CHANGE date_e date_e DATE NOT NULL');
        $this->addSql('ALTER TABLE galerie DROP FOREIGN KEY FK_9E7D1590A76ED395');
        $this->addSql('DROP INDEX UNIQ_9E7D1590A76ED395 ON galerie');
        $this->addSql('ALTER TABLE galerie DROP user_id');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849559D86650F');
        $this->addSql('DROP INDEX IDX_42C849559D86650F ON reservation');
        $this->addSql('ALTER TABLE reservation DROP user_id_id');
    }
}
