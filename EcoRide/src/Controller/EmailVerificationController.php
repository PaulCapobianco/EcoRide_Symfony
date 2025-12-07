<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Vérifie l’e-mail (ou un changement d’e-mail) à partir d’un token.
 */
class EmailVerificationController extends AbstractController
{
    #[Route('/verification-email/{token}', name: 'verify_email', methods: ['GET'])]
    public function verify(string $token, EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Utilisateur::class);

        /** @var Utilisateur|null $user */
        $user = $repo->findOneBy(['verificationToken' => $token]);

        if (!$user instanceof Utilisateur) {
            $this->addFlash('danger', 'Lien de vérification invalide ou expiré.');
            return $this->redirectToRoute('login');
        }

        $pendingEmail = $user->getPendingEmail();
        $isEmailChange = $pendingEmail !== null;

        if ($isEmailChange) {
            $existing = $repo->findOneBy(['email' => $pendingEmail]);
            if ($existing instanceof Utilisateur && $existing->getId() !== $user->getId()) {
                $user
                    ->setPendingEmail(null)
                    ->setVerificationToken(null)
                    ->setVerificationRequestedAt(null);

                $em->flush();

                $this->addFlash('danger', 'Cette adresse e-mail est déjà utilisée. Merci d\'en choisir une autre.');
                return $this->redirectToRoute('profile_edit');
            }

            $user
                ->setEmail($pendingEmail)
                ->setPendingEmail(null);
        }

        $user
            ->setEmailVerified(true)
            ->setEmailVerifiedAt(new \DateTimeImmutable())
            ->setVerificationToken(null)
            ->setVerificationRequestedAt(null);

        $em->flush();

        $this->addFlash(
            'success',
            $isEmailChange
                ? 'Votre nouvelle adresse e-mail est validée.'
                : 'Votre adresse e-mail est vérifiée. Vous pouvez vous connecter.'
        );

        return $isEmailChange
            ? $this->redirectToRoute('profile')
            : $this->redirectToRoute('login');
    }
}
