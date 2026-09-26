<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260926120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add optional image filename to item';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item ADD image_filename VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item DROP image_filename');
    }
}
