<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210145028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonne DROP FOREIGN KEY FK_76328BF0BF396750');
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE artiste DROP FOREIGN KEY FK_9C07354FBF396750');
        $this->addSql('ALTER TABLE professeur DROP FOREIGN KEY FK_17A55299BF396750');
        $this->addSql('DROP TABLE abonne');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE artiste');
        $this->addSql('DROP TABLE professeur');
        $this->addSql('ALTER TABLE article ADD image_path VARCHAR(255) DEFAULT NULL, DROP ensemble_photos, CHANGE desc_a desc_a LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE magasin CHANGE photo_m photo_m VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD nb_place INT NOT NULL, DROP prix_e');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', ADD specialite VARCHAR(255) DEFAULT NULL, ADD point INT DEFAULT NULL, ADD cin VARCHAR(255) DEFAULT NULL, DROP role, DROP dtype, CHANGE email email VARCHAR(180) NOT NULL, CHANGE address address LONGTEXT NOT NULL, CHANGE verification_code verification_code VARCHAR(255) DEFAULT NULL, CHANGE is_verified is_verified TINYINT(1) DEFAULT NULL, CHANGE user_agent user_agent VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonne (id INT NOT NULL, points INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE artiste (id INT NOT NULL, cin INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE professeur (id INT NOT NULL, specialite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE abonne ADD CONSTRAINT FK_76328BF0BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artiste ADD CONSTRAINT FK_9C07354FBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE professeur ADD CONSTRAINT FK_17A55299BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article ADD ensemble_photos LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP image_path, CHANGE desc_a desc_a VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE magasin CHANGE photo_m photo_m VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD prix_e DOUBLE PRECISION NOT NULL, DROP nb_place');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user ADD role VARCHAR(255) NOT NULL, ADD dtype VARCHAR(255) NOT NULL, DROP roles, DROP specialite, DROP point, DROP cin, CHANGE email email VARCHAR(255) NOT NULL, CHANGE address address VARCHAR(255) NOT NULL, CHANGE verification_code verification_code VARCHAR(255) NOT NULL, CHANGE is_verified is_verified TINYINT(1) NOT NULL, CHANGE user_agent user_agent VARCHAR(255) NOT NULL');
    }
}
