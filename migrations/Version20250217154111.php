<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217154111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F7294869C');
        $this->addSql('DROP TABLE image');
        $this->addSql('ALTER TABLE article CHANGE image_path image_path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE commande ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C853CD175');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CA76ED395');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event CHANGE date_e date_e DATETIME NOT NULL, CHANGE photo_e photo_e VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE magasin CHANGE photo_m photo_m VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA927ECF78B0');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA927ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation CHANGE libelle libelle VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_C53D045F7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE article CHANGE image_path image_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('DROP INDEX IDX_6EEAA67DA76ED395 ON commande');
        $this->addSql('ALTER TABLE commande DROP user_id');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CA76ED395');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C853CD175');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE event CHANGE date_e date_e DATE NOT NULL, CHANGE photo_e photo_e VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE magasin CHANGE photo_m photo_m VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA927ECF78B0');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA927ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE reservation CHANGE libelle libelle VARCHAR(255) DEFAULT NULL');
    }
}
