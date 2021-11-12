<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211107184642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_extra_info ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_extra_info ADD CONSTRAINT FK_EC2E0F1CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC2E0F1CA76ED395 ON order_extra_info (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_extra_info DROP FOREIGN KEY FK_EC2E0F1CA76ED395');
        $this->addSql('DROP INDEX UNIQ_EC2E0F1CA76ED395 ON order_extra_info');
        $this->addSql('ALTER TABLE order_extra_info DROP user_id');
    }
}
