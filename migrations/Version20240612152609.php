<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612152609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_text (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, campaing_structure_id INT DEFAULT NULL, content VARCHAR(255) DEFAULT NULL, INDEX IDX_F2DEC0CC4B89032C (post_id), INDEX IDX_F2DEC0CCA0A0399A (campaing_structure_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_text ADD CONSTRAINT FK_F2DEC0CC4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_text ADD CONSTRAINT FK_F2DEC0CCA0A0399A FOREIGN KEY (campaing_structure_id) REFERENCES campaign_structure (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_text DROP FOREIGN KEY FK_F2DEC0CC4B89032C');
        $this->addSql('ALTER TABLE post_text DROP FOREIGN KEY FK_F2DEC0CCA0A0399A');
        $this->addSql('DROP TABLE post_text');
    }
}
