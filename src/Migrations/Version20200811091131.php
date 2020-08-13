<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200811091131 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE doctor_assignement (id INT AUTO_INCREMENT NOT NULL, id_patient_id INT NOT NULL, id_doctor_id INT DEFAULT NULL, request_date DATETIME NOT NULL, status VARCHAR(100) NOT NULL, disabled TINYINT(1) NOT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_606D0791CE0312AE (id_patient_id), INDEX IDX_606D07917C14730 (id_doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D0791CE0312AE FOREIGN KEY (id_patient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D07917C14730 FOREIGN KEY (id_doctor_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE doctor_assignement');
    }
}
