<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211108021821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_extra_info DROP INDEX UNIQ_EC2E0F1CA76ED395, ADD INDEX IDX_EC2E0F1CA76ED395 (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC2E0F1C8D9F6D38 ON order_extra_info (order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_extra_info DROP INDEX IDX_EC2E0F1CA76ED395, ADD UNIQUE INDEX UNIQ_EC2E0F1CA76ED395 (user_id)');
        $this->addSql('DROP INDEX UNIQ_EC2E0F1C8D9F6D38 ON order_extra_info');
    }
}
