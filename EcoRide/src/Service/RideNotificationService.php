<?php

namespace App\Service;

use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

/**
 * Centralise l'envoi des e-mails liés aux covoiturages (annulation, feedback passager).
 */
class RideNotificationService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private string $fromAddress = 'no-reply@ecoride.test'
    ) {
    }

    public function notifyFeedbackRequests(Covoiturage $covoiturage, string $feedbackUrl): void
    {
        foreach ($covoiturage->getParticipations() as $participation) {
            $passenger = $participation->getUtilisateur();
            if (!$passenger instanceof Utilisateur || !$passenger->getEmail()) {
                continue;
            }

            $html = $this->twig->render('emails/ride_feedback_request.html.twig', [
                'passenger'   => $passenger,
                'covoiturage' => $covoiturage,
                'feedbackUrl' => $feedbackUrl,
            ]);

            $email = (new Email())
                ->from($this->fromAddress)
                ->to($passenger->getEmail())
                ->subject('Confirmez votre trajet EcoRide')
                ->text(strip_tags($html))
                ->html($html);

            $this->mailer->send($email);
        }
    }

    public function notifyCancellation(Covoiturage $covoiturage): void
    {
        foreach ($covoiturage->getParticipations() as $participation) {
            $passenger = $participation->getUtilisateur();
            if (!$passenger instanceof Utilisateur || !$passenger->getEmail()) {
                continue;
            }

            $html = $this->twig->render('emails/ride_cancelled.html.twig', [
                'passenger'   => $passenger,
                'covoiturage' => $covoiturage,
            ]);

            $email = (new Email())
                ->from($this->fromAddress)
                ->to($passenger->getEmail())
                ->subject('Annulation d’un covoiturage auquel vous participiez')
                ->text(strip_tags($html))
                ->html($html);

            $this->mailer->send($email);
        }
    }
}
