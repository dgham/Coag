<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200811093722 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE treatment ADD type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C31C54C8C93 FOREIGN KEY (type_id) REFERENCES medication_type (id)');
        $this->addSql('CREATE INDEX IDX_98013C31C54C8C93 ON treatment (type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C31C54C8C93');
        $this->addSql('DROP INDEX IDX_98013C31C54C8C93 ON treatment');
        $this->addSql('ALTER TABLE treatment DROP type_id');
    }
}
