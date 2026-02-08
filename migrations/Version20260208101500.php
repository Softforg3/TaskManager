<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260208101500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add updated_at column to tasks table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks ADD updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('UPDATE tasks SET updated_at = created_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks DROP COLUMN updated_at');
    }
}
