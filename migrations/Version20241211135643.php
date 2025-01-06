<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241211135643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE web_link_detail (id INT AUTO_INCREMENT NOT NULL, web_link_id INT NOT NULL, created_date DATETIME NOT NULL, headers TEXT DEFAULT NULL, content MEDIUMTEXT DEFAULT NULL, status_code INT NOT NULL, UNIQUE INDEX UNIQ_46F5178D450BACEF (web_link_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE web_link_detail ADD CONSTRAINT FK_46F5178D450BACEF FOREIGN KEY (web_link_id) REFERENCES web_link (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE web_link_detail DROP FOREIGN KEY FK_46F5178D450BACEF');
        $this->addSql('DROP TABLE web_link_detail');
    }
}
