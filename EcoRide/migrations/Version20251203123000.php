<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * US11 : suivi démarrage/arrivée + validations passagers.
 */
final class Version20251203123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes started_at/finished_at sur covoiturage et des champs de validation sur participation.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE covoiturage ADD started_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD finished_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE participation ADD confirmation_status VARCHAR(20) DEFAULT \'PENDING\' NOT NULL, ADD confirmation_comment VARCHAR(255) DEFAULT NULL, ADD confirmation_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('UPDATE participation SET confirmation_status = \'PENDING\' WHERE confirmation_status IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE covoiturage DROP started_at, DROP finished_at');
        $this->addSql('ALTER TABLE participation DROP confirmation_status, DROP confirmation_comment, DROP confirmation_at');
    }
}
