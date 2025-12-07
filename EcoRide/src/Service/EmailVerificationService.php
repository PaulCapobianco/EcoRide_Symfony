<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * Prépare et envoie les e-mails de vérification de compte ou de changement d'adresse.
 */
class EmailVerificationService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Environment $twig
    ) {
    }

    /**
     * Prépare le token + l'envoi du mail de vérification (inscription ou changement d'e-mail).
     */
    public function sendVerificationEmail(Utilisateur $user, string $targetEmail, bool $isEmailChange = false): void
    {
        $token = bin2hex(random_bytes(32));

        $user
            ->setVerificationToken($token)
            ->setVerificationRequestedAt(new \DateTimeImmutable());

        $verificationUrl = $this->urlGenerator->generate(
            'verify_email',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $subject = $isEmailChange
            ? 'Confirmez votre nouvelle adresse e-mail'
            : 'Activez votre compte EcoRide';

        $html = $this->twig->render('emails/verification.html.twig', [
            'user'            => $user,
            'verificationUrl' => $verificationUrl,
            'targetEmail'     => $targetEmail,
            'isEmailChange'   => $isEmailChange,
        ]);

        $email = (new Email())
            ->from('no-reply@ecoride.test')
            ->to($targetEmail)
            ->subject($subject)
            ->text(strip_tags($html))
            ->html($html);

        $this->mailer->send($email);
    }
}
