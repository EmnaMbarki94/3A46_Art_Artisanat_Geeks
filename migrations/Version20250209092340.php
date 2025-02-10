<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209092340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849553256915B');
        $this->addSql('DROP INDEX IDX_42C849553256915B ON reservation');
        $this->addSql('ALTER TABLE reservation CHANGE nb_place nb_place INT DEFAULT NULL, CHANGE relation_id event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495571F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_42C8495571F7E88B ON reservation (event_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495571F7E88B');
        $this->addSql('DROP INDEX IDX_42C8495571F7E88B ON reservation');
        $this->addSql('ALTER TABLE reservation CHANGE nb_place nb_place INT NOT NULL, CHANGE event_id relation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849553256915B FOREIGN KEY (relation_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_42C849553256915B ON reservation (relation_id)');
    }
}
