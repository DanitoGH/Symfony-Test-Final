<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211111175810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_status_logger ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_status_logger ADD CONSTRAINT FK_8857F36C6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_8857F36C6BF700BD ON order_status_logger (status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_status_logger DROP FOREIGN KEY FK_8857F36C6BF700BD');
        $this->addSql('DROP INDEX IDX_8857F36C6BF700BD ON order_status_logger');
        $this->addSql('ALTER TABLE order_status_logger DROP status_id');
    }
}
