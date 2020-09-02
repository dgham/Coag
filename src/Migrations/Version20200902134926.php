<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200902134926 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE eatinghabits DROP FOREIGN KEY FK_CAEFC6E963CE59A0');
        $this->addSql('DROP INDEX IDX_CAEFC6E963CE59A0 ON eatinghabits');
        $this->addSql('ALTER TABLE eatinghabits DROP food_description_id');
        $this->addSql('ALTER TABLE patient ADD pathology VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE eatinghabits ADD food_description_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE eatinghabits ADD CONSTRAINT FK_CAEFC6E963CE59A0 FOREIGN KEY (food_description_id) REFERENCES foods (id)');
        $this->addSql('CREATE INDEX IDX_CAEFC6E963CE59A0 ON eatinghabits (food_description_id)');
        $this->addSql('ALTER TABLE patient DROP pathology');
    }
}
