<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209141845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9CA76ED395 ON cours (user_id)');
        $this->addSql('ALTER TABLE quiz_attempt ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz_attempt ADD CONSTRAINT FK_AB6AFC6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AB6AFC6A76ED395 ON quiz_attempt (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CA76ED395');
        $this->addSql('DROP INDEX IDX_FDCA8C9CA76ED395 ON cours');
        $this->addSql('ALTER TABLE cours DROP user_id');
        $this->addSql('ALTER TABLE quiz_attempt DROP FOREIGN KEY FK_AB6AFC6A76ED395');
        $this->addSql('DROP INDEX IDX_AB6AFC6A76ED395 ON quiz_attempt');
        $this->addSql('ALTER TABLE quiz_attempt DROP user_id');
    }
}
