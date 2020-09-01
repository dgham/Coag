<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200831201549 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doctor_assignement CHANGE invitation_token invitation_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE eatinghabits ADD breakfast_food VARCHAR(255) DEFAULT NULL, ADD lunch_food VARCHAR(255) DEFAULT NULL, ADD dinner_food VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE treatment DROP type');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doctor_assignement CHANGE invitation_token invitation_token VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE eatinghabits DROP breakfast_food, DROP lunch_food, DROP dinner_food');
        $this->addSql('ALTER TABLE treatment ADD type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
