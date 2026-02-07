<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260207122000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create event_store table for Event Sourcing';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE event_store (id INT AUTO_INCREMENT NOT NULL, aggregate_id VARCHAR(36) NOT NULL, aggregate_type VARCHAR(50) NOT NULL, event_type VARCHAR(100) NOT NULL, payload JSON NOT NULL, occurred_at DATETIME NOT NULL, INDEX idx_aggregate (aggregate_id, aggregate_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE event_store');
    }
}
