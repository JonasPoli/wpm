<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612150911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campaign_structure (id INT AUTO_INCREMENT NOT NULL, campaign_id INT DEFAULT NULL, font VARCHAR(255) DEFAULT NULL, color_r INT DEFAULT NULL, color_g INT NOT NULL, color_b INT NOT NULL, shadow_xshift INT NOT NULL, shadow_yshif INT NOT NULL, font_size INT NOT NULL, line_height DOUBLE PRECISION NOT NULL, box_x INT NOT NULL, box_y INT NOT NULL, box_width INT NOT NULL, box_height INT NOT NULL, align_x VARCHAR(255) NOT NULL, align_y VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_AF892B91F639F774 (campaign_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campaign_structure ADD CONSTRAINT FK_AF892B91F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign_structure DROP FOREIGN KEY FK_AF892B91F639F774');
        $this->addSql('DROP TABLE campaign_structure');
    }
}
