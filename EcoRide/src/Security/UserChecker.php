<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Vérifie l’état d’un utilisateur avant l’authentification (compte actif, email vérifié…).
 */
class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException(
                'Ce compte est suspendu. Contactez un administrateur.'
            );
        }

        if (!$user->isEmailVerified()) {
            throw new CustomUserMessageAccountStatusException(
                'Merci de vérifier votre adresse e-mail via le lien de confirmation envoyé.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Rien de spécifique en post-authentification pour le moment.
    }
}
