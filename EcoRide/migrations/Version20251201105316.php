<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration neutralisée : plus aucune modification de schéma.
 */
final class Version20251201105316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration neutralisée : pas de changement de schéma (alignement Participation <-> DB).';
    }

    public function up(Schema $schema): void
    {
        // Intentionnellement vide : aucun SQL exécuté.
    }

    public function down(Schema $schema): void
    {
        // Intentionnellement vide : aucun rollback nécessaire.
    }
}
