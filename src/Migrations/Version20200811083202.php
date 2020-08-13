<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200811083202 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE medication_type (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, type VARCHAR(55) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_D70D072AB03A8386 (created_by_id), INDEX IDX_D70D072A896DBBDE (updated_by_id), INDEX IDX_D70D072A2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE medication_type ADD CONSTRAINT FK_D70D072AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medication_type ADD CONSTRAINT FK_D70D072A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medication_type ADD CONSTRAINT FK_D70D072A2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE medication_type');
    }
}
