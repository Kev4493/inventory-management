<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260516110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add inventory number to item';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item ADD inventory_number VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item DROP inventory_number');
    }
}
