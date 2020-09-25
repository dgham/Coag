<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200914151001 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE foods (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, quantity VARCHAR(10) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_3803909DB03A8386 (created_by_id), INDEX IDX_3803909D896DBBDE (updated_by_id), INDEX IDX_3803909D2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medication_type (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, type VARCHAR(55) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_D70D072AB03A8386 (created_by_id), INDEX IDX_D70D072A896DBBDE (updated_by_id), INDEX IDX_D70D072A2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE speciality (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, speciality_name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_F3D7A08EB03A8386 (created_by_id), INDEX IDX_F3D7A08E896DBBDE (updated_by_id), INDEX IDX_F3D7A08E2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medication_list (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, effect VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_1F1BA81BB03A8386 (created_by_id), INDEX IDX_1F1BA81B896DBBDE (updated_by_id), INDEX IDX_1F1BA81B2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doctor_assignement (id INT AUTO_INCREMENT NOT NULL, id_patient_id INT NOT NULL, id_doctor_id INT DEFAULT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, request_date DATETIME NOT NULL, status VARCHAR(100) NOT NULL, enabled TINYINT(1) NOT NULL, removed TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, invitation_token VARCHAR(255) DEFAULT NULL, INDEX IDX_606D0791CE0312AE (id_patient_id), INDEX IDX_606D07917C14730 (id_doctor_id), INDEX IDX_606D0791B03A8386 (created_by_id), INDEX IDX_606D0791896DBBDE (updated_by_id), INDEX IDX_606D07912BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE foods ADD CONSTRAINT FK_3803909DB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE foods ADD CONSTRAINT FK_3803909D896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE foods ADD CONSTRAINT FK_3803909D2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medication_type ADD CONSTRAINT FK_D70D072AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medication_type ADD CONSTRAINT FK_D70D072A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medication_type ADD CONSTRAINT FK_D70D072A2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE speciality ADD CONSTRAINT FK_F3D7A08EB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE speciality ADD CONSTRAINT FK_F3D7A08E896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE speciality ADD CONSTRAINT FK_F3D7A08E2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medication_list ADD CONSTRAINT FK_1F1BA81BB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medication_list ADD CONSTRAINT FK_1F1BA81B896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medication_list ADD CONSTRAINT FK_1F1BA81B2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D0791CE0312AE FOREIGN KEY (id_patient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D07917C14730 FOREIGN KEY (id_doctor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D0791B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D0791896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D07912BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD country_id INT DEFAULT NULL, ADD qr_code VARCHAR(255) DEFAULT NULL, DROP country');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649F92F3E70 ON user (country_id)');
        $this->addSql('ALTER TABLE eatinghabits ADD breakfast_food VARCHAR(255) DEFAULT NULL, ADD lunch_food VARCHAR(255) DEFAULT NULL, ADD dinner_food VARCHAR(255) DEFAULT NULL, DROP description');
        $this->addSql('ALTER TABLE treatment ADD medication_type VARCHAR(255) DEFAULT NULL, DROP type');
        $this->addSql('ALTER TABLE doctor ADD speciality_id INT DEFAULT NULL, DROP speciality');
        $this->addSql('ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36A3B5A08D7 FOREIGN KEY (speciality_id) REFERENCES speciality (id)');
        $this->addSql('CREATE INDEX IDX_1FC0F36A3B5A08D7 ON doctor (speciality_id)');
        $this->addSql('ALTER TABLE diagnostic ADD device_date VARCHAR(100) DEFAULT NULL, ADD details VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE patient ADD pathology VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doctor DROP FOREIGN KEY FK_1FC0F36A3B5A08D7');
        $this->addSql('DROP TABLE foods');
        $this->addSql('DROP TABLE medication_type');
        $this->addSql('DROP TABLE speciality');
        $this->addSql('DROP TABLE medication_list');
        $this->addSql('DROP TABLE doctor_assignement');
        $this->addSql('ALTER TABLE diagnostic DROP device_date, DROP details');
        $this->addSql('DROP INDEX IDX_1FC0F36A3B5A08D7 ON doctor');
        $this->addSql('ALTER TABLE doctor ADD speciality VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP speciality_id');
        $this->addSql('ALTER TABLE eatinghabits ADD description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP breakfast_food, DROP lunch_food, DROP dinner_food');
        $this->addSql('ALTER TABLE patient DROP pathology');
        $this->addSql('ALTER TABLE treatment ADD type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP medication_type');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('DROP INDEX IDX_8D93D649F92F3E70 ON user');
        $this->addSql('ALTER TABLE user ADD country VARCHAR(85) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP country_id, DROP qr_code');
    }
}
