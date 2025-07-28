<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240617144755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_history (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, event_description VARCHAR(255) DEFAULT NULL, occurred_in DATETIME DEFAULT NULL, INDEX IDX_1A52C7784B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_history ADD CONSTRAINT FK_1A52C7784B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post ADD facebook_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_history DROP FOREIGN KEY FK_1A52C7784B89032C');
        $this->addSql('DROP TABLE post_history');
        $this->addSql('ALTER TABLE post DROP facebook_id');
    }
}
