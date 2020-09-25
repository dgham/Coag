<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200924195728 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, created_id INT DEFAULT NULL, updated_id INT DEFAULT NULL, removed_id INT DEFAULT NULL, country_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', gender VARCHAR(50) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, user_type VARCHAR(55) NOT NULL, birth_date DATE DEFAULT NULL, session_timeout DATETIME DEFAULT NULL, zip_code VARCHAR(100) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(100) DEFAULT NULL, multi_session TINYINT(1) DEFAULT NULL, language VARCHAR(2) DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, remove TINYINT(1) NOT NULL, qr_code VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), INDEX IDX_8D93D6495EE01E44 (created_id), INDEX IDX_8D93D649960CC7F3 (updated_id), INDEX IDX_8D93D649903F1AC9 (removed_id), INDEX IDX_8D93D649F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE translation (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, word VARCHAR(255) NOT NULL, en VARCHAR(255) DEFAULT NULL, fr VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, remove TINYINT(1) NOT NULL, INDEX IDX_B469456FB03A8386 (created_by_id), INDEX IDX_B469456F896DBBDE (updated_by_id), INDEX IDX_B469456F2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, code VARCHAR(2) NOT NULL, long_code VARCHAR(3) DEFAULT NULL, prefix VARCHAR(6) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, remove TINYINT(1) NOT NULL, INDEX IDX_5373C966B03A8386 (created_by_id), INDEX IDX_5373C966896DBBDE (updated_by_id), INDEX IDX_5373C9662BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, upadated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, patient_id_id INT NOT NULL, comment LONGTEXT NOT NULL, created_at DATETIME NOT NULL, upadted_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, remove TINYINT(1) NOT NULL, INDEX IDX_CFBDFA14B03A8386 (created_by_id), INDEX IDX_CFBDFA14B3FC4B33 (upadated_by_id), INDEX IDX_CFBDFA142BD701DA (removed_by_id), INDEX IDX_CFBDFA14EA724598 (patient_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE treatment (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, patient_id INT NOT NULL, name VARCHAR(180) NOT NULL, picture VARCHAR(255) DEFAULT NULL, dosage VARCHAR(55) NOT NULL, periode VARCHAR(55) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, remove TINYINT(1) NOT NULL, drug_type VARCHAR(255) DEFAULT NULL, INDEX IDX_98013C31B03A8386 (created_by_id), INDEX IDX_98013C31896DBBDE (updated_by_id), INDEX IDX_98013C312BD701DA (removed_by_id), INDEX IDX_98013C316B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE speciality (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, speciality_name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_F3D7A08EB03A8386 (created_by_id), INDEX IDX_F3D7A08E896DBBDE (updated_by_id), INDEX IDX_F3D7A08E2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, user_agent VARCHAR(255) NOT NULL, is_valide TINYINT(1) NOT NULL, ip_address VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_D044D5D4B03A8386 (created_by_id), INDEX IDX_D044D5D4896DBBDE (updated_by_id), INDEX IDX_D044D5D42BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drug_type (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, type VARCHAR(55) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_B58B42AAB03A8386 (created_by_id), INDEX IDX_B58B42AA896DBBDE (updated_by_id), INDEX IDX_B58B42AA2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hospital (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, type VARCHAR(55) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, web_site VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, INDEX IDX_4282C85BB03A8386 (created_by_id), INDEX IDX_4282C85B896DBBDE (updated_by_id), INDEX IDX_4282C85B2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, push_device_id_id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, body VARCHAR(255) NOT NULL, data VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, readed TINYINT(1) NOT NULL, INDEX IDX_BF5476CA4C2F2A10 (push_device_id_id), INDEX IDX_BF5476CAB03A8386 (created_by_id), INDEX IDX_BF5476CA896DBBDE (updated_by_id), INDEX IDX_BF5476CA2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doctor_assignement (id INT AUTO_INCREMENT NOT NULL, id_patient_id INT NOT NULL, id_doctor_id INT DEFAULT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, request_date DATETIME NOT NULL, status VARCHAR(100) NOT NULL, enabled TINYINT(1) NOT NULL, removed TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, invitation_token VARCHAR(255) DEFAULT NULL, INDEX IDX_606D0791CE0312AE (id_patient_id), INDEX IDX_606D07917C14730 (id_doctor_id), INDEX IDX_606D0791B03A8386 (created_by_id), INDEX IDX_606D0791896DBBDE (updated_by_id), INDEX IDX_606D07912BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_D43829F7B03A8386 (created_by_id), INDEX IDX_D43829F7896DBBDE (updated_by_id), INDEX IDX_D43829F72BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doctor (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, hospital_id INT DEFAULT NULL, speciality_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, affiliate TINYINT(1) NOT NULL, registration_number VARCHAR(100) NOT NULL, INDEX IDX_1FC0F36AB03A8386 (created_by_id), INDEX IDX_1FC0F36A896DBBDE (updated_by_id), INDEX IDX_1FC0F36A2BD701DA (removed_by_id), INDEX IDX_1FC0F36A63DBB69 (hospital_id), INDEX IDX_1FC0F36A3B5A08D7 (speciality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eating_habit (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, food VARCHAR(1024) NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, remove TINYINT(1) NOT NULL, INDEX IDX_A1682A3DB03A8386 (created_by_id), INDEX IDX_A1682A3D896DBBDE (updated_by_id), INDEX IDX_A1682A3D2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE asset (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, text_value VARCHAR(255) NOT NULL, access VARCHAR(55) NOT NULL, type VARCHAR(255) NOT NULL, file_path VARCHAR(255) NOT NULL, file_type VARCHAR(100) NOT NULL, file_size VARCHAR(255) NOT NULL, duration INT DEFAULT NULL, resolution VARCHAR(100) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, remove TINYINT(1) NOT NULL, INDEX IDX_2AF5A5CB03A8386 (created_by_id), INDEX IDX_2AF5A5C896DBBDE (updated_by_id), INDEX IDX_2AF5A5C2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE measure (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, indication VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, device_date VARCHAR(100) DEFAULT NULL, details VARCHAR(255) DEFAULT NULL, INDEX IDX_80071925B03A8386 (created_by_id), INDEX IDX_80071925896DBBDE (updated_by_id), INDEX IDX_800719252BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drug (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, effect VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, picture VARCHAR(255) DEFAULT NULL, INDEX IDX_43EB7A3EB03A8386 (created_by_id), INDEX IDX_43EB7A3E896DBBDE (updated_by_id), INDEX IDX_43EB7A3E2BD701DA (removed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, removed_by_id INT DEFAULT NULL, assigned_by_id INT DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, size DOUBLE PRECISION DEFAULT NULL, proffesion VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, pathology VARCHAR(100) NOT NULL, INDEX IDX_1ADAD7EBB03A8386 (created_by_id), INDEX IDX_1ADAD7EB896DBBDE (updated_by_id), INDEX IDX_1ADAD7EB2BD701DA (removed_by_id), INDEX IDX_1ADAD7EB6E6F1246 (assigned_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, removed_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, os VARCHAR(100) NOT NULL, version VARCHAR(255) NOT NULL, modele VARCHAR(255) NOT NULL, uuid VARCHAR(255) NOT NULL, position VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, removed_at DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, INDEX IDX_92FB68EB03A8386 (created_by_id), INDEX IDX_92FB68E2BD701DA (removed_by_id), INDEX IDX_92FB68E896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6495EE01E44 FOREIGN KEY (created_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649960CC7F3 FOREIGN KEY (updated_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649903F1AC9 FOREIGN KEY (removed_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456FB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456F896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456F2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C966B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C966896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C9662BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14B3FC4B33 FOREIGN KEY (upadated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA142BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14EA724598 FOREIGN KEY (patient_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C31B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C31896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C312BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C316B899279 FOREIGN KEY (patient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE speciality ADD CONSTRAINT FK_F3D7A08EB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE speciality ADD CONSTRAINT FK_F3D7A08E896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE speciality ADD CONSTRAINT FK_F3D7A08E2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D42BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE drug_type ADD CONSTRAINT FK_B58B42AAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE drug_type ADD CONSTRAINT FK_B58B42AA896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE drug_type ADD CONSTRAINT FK_B58B42AA2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85BB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA4C2F2A10 FOREIGN KEY (push_device_id_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D0791CE0312AE FOREIGN KEY (id_patient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D07917C14730 FOREIGN KEY (id_doctor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D0791B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D0791896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor_assignement ADD CONSTRAINT FK_606D07912BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE food ADD CONSTRAINT FK_D43829F7B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE food ADD CONSTRAINT FK_D43829F7896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE food ADD CONSTRAINT FK_D43829F72BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36A2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36A63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36A3B5A08D7 FOREIGN KEY (speciality_id) REFERENCES speciality (id)');
        $this->addSql('ALTER TABLE eating_habit ADD CONSTRAINT FK_A1682A3DB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE eating_habit ADD CONSTRAINT FK_A1682A3D896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE eating_habit ADD CONSTRAINT FK_A1682A3D2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE measure ADD CONSTRAINT FK_80071925B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE measure ADD CONSTRAINT FK_80071925896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE measure ADD CONSTRAINT FK_800719252BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE drug ADD CONSTRAINT FK_43EB7A3EB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE drug ADD CONSTRAINT FK_43EB7A3E896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE drug ADD CONSTRAINT FK_43EB7A3E2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB6E6F1246 FOREIGN KEY (assigned_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E2BD701DA FOREIGN KEY (removed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6495EE01E44');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649960CC7F3');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649903F1AC9');
        $this->addSql('ALTER TABLE translation DROP FOREIGN KEY FK_B469456FB03A8386');
        $this->addSql('ALTER TABLE translation DROP FOREIGN KEY FK_B469456F896DBBDE');
        $this->addSql('ALTER TABLE translation DROP FOREIGN KEY FK_B469456F2BD701DA');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C966B03A8386');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C966896DBBDE');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C9662BD701DA');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14B03A8386');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14B3FC4B33');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA142BD701DA');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14EA724598');
        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C31B03A8386');
        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C31896DBBDE');
        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C312BD701DA');
        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C316B899279');
        $this->addSql('ALTER TABLE speciality DROP FOREIGN KEY FK_F3D7A08EB03A8386');
        $this->addSql('ALTER TABLE speciality DROP FOREIGN KEY FK_F3D7A08E896DBBDE');
        $this->addSql('ALTER TABLE speciality DROP FOREIGN KEY FK_F3D7A08E2BD701DA');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4B03A8386');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4896DBBDE');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D42BD701DA');
        $this->addSql('ALTER TABLE drug_type DROP FOREIGN KEY FK_B58B42AAB03A8386');
        $this->addSql('ALTER TABLE drug_type DROP FOREIGN KEY FK_B58B42AA896DBBDE');
        $this->addSql('ALTER TABLE drug_type DROP FOREIGN KEY FK_B58B42AA2BD701DA');
        $this->addSql('ALTER TABLE hospital DROP FOREIGN KEY FK_4282C85BB03A8386');
        $this->addSql('ALTER TABLE hospital DROP FOREIGN KEY FK_4282C85B896DBBDE');
        $this->addSql('ALTER TABLE hospital DROP FOREIGN KEY FK_4282C85B2BD701DA');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAB03A8386');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA896DBBDE');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA2BD701DA');
        $this->addSql('ALTER TABLE doctor_assignement DROP FOREIGN KEY FK_606D0791CE0312AE');
        $this->addSql('ALTER TABLE doctor_assignement DROP FOREIGN KEY FK_606D07917C14730');
        $this->addSql('ALTER TABLE doctor_assignement DROP FOREIGN KEY FK_606D0791B03A8386');
        $this->addSql('ALTER TABLE doctor_assignement DROP FOREIGN KEY FK_606D0791896DBBDE');
        $this->addSql('ALTER TABLE doctor_assignement DROP FOREIGN KEY FK_606D07912BD701DA');
        $this->addSql('ALTER TABLE food DROP FOREIGN KEY FK_D43829F7B03A8386');
        $this->addSql('ALTER TABLE food DROP FOREIGN KEY FK_D43829F7896DBBDE');
        $this->addSql('ALTER TABLE food DROP FOREIGN KEY FK_D43829F72BD701DA');
        $this->addSql('ALTER TABLE doctor DROP FOREIGN KEY FK_1FC0F36AB03A8386');
        $this->addSql('ALTER TABLE doctor DROP FOREIGN KEY FK_1FC0F36A896DBBDE');
        $this->addSql('ALTER TABLE doctor DROP FOREIGN KEY FK_1FC0F36A2BD701DA');
        $this->addSql('ALTER TABLE eating_habit DROP FOREIGN KEY FK_A1682A3DB03A8386');
        $this->addSql('ALTER TABLE eating_habit DROP FOREIGN KEY FK_A1682A3D896DBBDE');
        $this->addSql('ALTER TABLE eating_habit DROP FOREIGN KEY FK_A1682A3D2BD701DA');
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5CB03A8386');
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C896DBBDE');
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5C2BD701DA');
        $this->addSql('ALTER TABLE measure DROP FOREIGN KEY FK_80071925B03A8386');
        $this->addSql('ALTER TABLE measure DROP FOREIGN KEY FK_80071925896DBBDE');
        $this->addSql('ALTER TABLE measure DROP FOREIGN KEY FK_800719252BD701DA');
        $this->addSql('ALTER TABLE drug DROP FOREIGN KEY FK_43EB7A3EB03A8386');
        $this->addSql('ALTER TABLE drug DROP FOREIGN KEY FK_43EB7A3E896DBBDE');
        $this->addSql('ALTER TABLE drug DROP FOREIGN KEY FK_43EB7A3E2BD701DA');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBB03A8386');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EB896DBBDE');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EB2BD701DA');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EB6E6F1246');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68EB03A8386');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E2BD701DA');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E896DBBDE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('ALTER TABLE doctor DROP FOREIGN KEY FK_1FC0F36A3B5A08D7');
        $this->addSql('ALTER TABLE doctor DROP FOREIGN KEY FK_1FC0F36A63DBB69');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA4C2F2A10');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE translation');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE treatment');
        $this->addSql('DROP TABLE speciality');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE drug_type');
        $this->addSql('DROP TABLE hospital');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE doctor_assignement');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE doctor');
        $this->addSql('DROP TABLE eating_habit');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE measure');
        $this->addSql('DROP TABLE drug');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE refresh_tokens');
    }
}
