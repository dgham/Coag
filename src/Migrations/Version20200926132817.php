<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200926132817 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE eatinghabits');
        $this->addSql('ALTER TABLE treatment ADD drug_type_id INT NOT NULL, DROP picture, DROP drug_type');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C31A8B6A1C FOREIGN KEY (drug_type_id) REFERENCES drug_type (id)');
        $this->addSql('CREATE INDEX IDX_98013C31A8B6A1C ON treatment (drug_type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE eatinghabits (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, remove TINYINT(1) NOT NULL, INDEX IDX_CAEFC6E9896DBBDE (updated_by_id), INDEX IDX_CAEFC6E92BD701DA (removed_by_id), INDEX IDX_CAEFC6E9B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE eatinghabits ADD CONSTRAINT FK_CAEFC6E92BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE eatinghabits ADD CONSTRAINT FK_CAEFC6E9896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE eatinghabits ADD CONSTRAINT FK_CAEFC6E9B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C31A8B6A1C');
        $this->addSql('DROP INDEX IDX_98013C31A8B6A1C ON treatment');
        $this->addSql('ALTER TABLE treatment ADD picture VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD drug_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP drug_type_id');
    }
}
