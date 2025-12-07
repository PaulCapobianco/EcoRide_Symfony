<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251204120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout colonne actif sur utilisateur (suspension de compte).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur ADD actif TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('UPDATE utilisateur SET actif = 1 WHERE actif IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur DROP actif');
    }
}
