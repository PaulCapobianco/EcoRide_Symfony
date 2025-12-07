<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajout des colonnes liées à la vérification d'e-mail (inscription + changement d'adresse).
 */
final class Version20251203120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes de vérification d’e-mail sur utilisateur (token, pending email, statut).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur ADD email_verifie TINYINT(1) DEFAULT 1 NOT NULL, ADD verification_token VARCHAR(64) DEFAULT NULL, ADD verification_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD pending_email VARCHAR(50) DEFAULT NULL, ADD email_verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('UPDATE utilisateur SET email_verifie = 1 WHERE email_verifie IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur DROP email_verifie, DROP verification_token, DROP verification_requested_at, DROP pending_email, DROP email_verified_at');
    }
}
