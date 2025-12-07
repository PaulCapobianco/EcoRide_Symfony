<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251205100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du flux de réinitialisation de mot de passe (token + date).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur ADD reset_password_token VARCHAR(64) DEFAULT NULL, ADD reset_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur DROP reset_password_token, DROP reset_requested_at');
    }
}
