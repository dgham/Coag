<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201129090744 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C31A8B6A1C');
        $this->addSql('DROP INDEX IDX_98013C31A8B6A1C ON treatment');
        $this->addSql('ALTER TABLE treatment ADD drug_type VARCHAR(255) DEFAULT NULL, DROP drug_type_id');
        $this->addSql('ALTER TABLE notification ADD recived_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE167033A FOREIGN KEY (recived_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAE167033A ON notification (recived_user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE167033A');
        $this->addSql('DROP INDEX IDX_BF5476CAE167033A ON notification');
        $this->addSql('ALTER TABLE notification DROP recived_user_id');
        $this->addSql('ALTER TABLE treatment ADD drug_type_id INT NOT NULL, DROP drug_type');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C31A8B6A1C FOREIGN KEY (drug_type_id) REFERENCES drug_type (id)');
        $this->addSql('CREATE INDEX IDX_98013C31A8B6A1C ON treatment (drug_type_id)');
    }
}
