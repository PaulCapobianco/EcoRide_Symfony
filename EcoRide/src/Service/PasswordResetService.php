<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * Génère les tokens + e-mails de réinitialisation de mot de passe.
 */
class PasswordResetService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Environment $twig
    ) {
    }

    /**
     * Déclenche la génération d'un token et prépare le mail contenant le lien.
     */
    public function sendResetLink(Utilisateur $user): void
    {
        $token = bin2hex(random_bytes(32));

        $user
            ->setResetPasswordToken($token)
            ->setResetRequestedAt(new \DateTimeImmutable());

        $resetUrl = $this->urlGenerator->generate(
            'reset_password',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $html = $this->twig->render('emails/password_reset.html.twig', [
            'user'     => $user,
            'resetUrl' => $resetUrl,
        ]);

        $email = (new Email())
            ->from('no-reply@ecoride.test')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe EcoRide')
            ->text(strip_tags($html))
            ->html($html);

        $this->mailer->send($email);
    }
}
