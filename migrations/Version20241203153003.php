<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241203153003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(250) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) DEFAULT NULL, token_verifiy VARCHAR(255) DEFAULT NULL, token_verify_date_created DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE web_link (id INT AUTO_INCREMENT NOT NULL, web_link_schedule_id INT NOT NULL, status_code INT DEFAULT NULL, date_visited DATETIME NOT NULL, INDEX IDX_F2AE94049FEF18FC (web_link_schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE web_link_schedule (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, link VARCHAR(2048) NOT NULL, INDEX IDX_CD10AFBDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE web_link ADD CONSTRAINT FK_F2AE94049FEF18FC FOREIGN KEY (web_link_schedule_id) REFERENCES web_link_schedule (id)');
        $this->addSql('ALTER TABLE web_link_schedule ADD CONSTRAINT FK_CD10AFBDA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE web_link DROP FOREIGN KEY FK_F2AE94049FEF18FC');
        $this->addSql('ALTER TABLE web_link_schedule DROP FOREIGN KEY FK_CD10AFBDA76ED395');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE web_link');
        $this->addSql('DROP TABLE web_link_schedule');
    }
}
