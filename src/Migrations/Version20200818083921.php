<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200818083921 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doctor_assignement ADD created_by_id INT NOT NULL, ADD updated_by_id INT NOT NULL, ADD removed_by_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD removed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D0791B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D0791896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D07912BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_606D0791B03A8386 ON doctor_assignement (created_by_id)');
        $this->addSql('CREATE INDEX IDX_606D0791896DBBDE ON doctor_assignement (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_606D07912BD701DA ON doctor_assignement (removed_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doctor_assignement DROP FOREIGN KEY FK_606D0791B03A8386');
        $this->addSql('ALTER TABLE doctor_assignement DROP FOREIGN KEY FK_606D0791896DBBDE');
        $this->addSql('ALTER TABLE doctor_assignement DROP FOREIGN KEY FK_606D07912BD701DA');
        $this->addSql('DROP INDEX IDX_606D0791B03A8386 ON doctor_assignement');
        $this->addSql('DROP INDEX IDX_606D0791896DBBDE ON doctor_assignement');
        $this->addSql('DROP INDEX IDX_606D07912BD701DA ON doctor_assignement');
        $this->addSql('ALTER TABLE doctor_assignement DROP created_by_id, DROP updated_by_id, DROP removed_by_id, DROP created_at, DROP updated_at, DROP removed_at');
    }
}
