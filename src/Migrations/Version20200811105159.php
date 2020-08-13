<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200811105159 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE speciality DROP FOREIGN KEY FK_F3D7A08EF7E1C953');
        $this->addSql('DROP INDEX IDX_F3D7A08EF7E1C953 ON speciality');
        $this->addSql('ALTER TABLE speciality CHANGE uodated_by_id updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE speciality ADD CONSTRAINT FK_F3D7A08E896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F3D7A08E896DBBDE ON speciality (updated_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE speciality DROP FOREIGN KEY FK_F3D7A08E896DBBDE');
        $this->addSql('DROP INDEX IDX_F3D7A08E896DBBDE ON speciality');
        $this->addSql('ALTER TABLE speciality CHANGE updated_by_id uodated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE speciality ADD CONSTRAINT FK_F3D7A08EF7E1C953 FOREIGN KEY (uodated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F3D7A08EF7E1C953 ON speciality (uodated_by_id)');
    }
}
