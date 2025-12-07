<?php

namespace App\Controller\Covoiturage;

use App\Entity\Covoiturage;
use App\Entity\Participation;
use App\Entity\Utilisateur;
use App\Service\ParticipationManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Participation passager : réserver, annuler, confirmer ou signaler un trajet.
 */
class CovoiturageParticipationController extends AbstractController
{
    public function __construct(private ParticipationManager $participationManager)
    {
    }

    /** Participer (réservation en crédits). */
    #[Route(
        '/covoiturages/{id}/participer',
        name: 'covoiturage_participer',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_USER')]
    public function participer(
        Request $request,
        Covoiturage $covoiturage,
        EntityManagerInterface $em
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            $this->addFlash('danger', 'Vous devez être connecté pour participer à un covoiturage.');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
        }

        if (!$this->isCsrfTokenValid('participer_' . $covoiturage->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide, veuillez réessayer.');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
        }

        if ($request->request->get('confirm') !== '1') {
            $this->addFlash('warning', 'Merci de confirmer l’utilisation de vos crédits.');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
        }

        $check = $this->participationManager->canParticipate($covoiturage, $user);
        if (!$check['canParticipate']) {
            $this->addFlash('warning', $check['reason'] ?? 'Action impossible.');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
        }

        $priceCredits = (int) $covoiturage->getPrixPersonne();
        $participation = $this->participationManager->registerParticipation($covoiturage, $user, $priceCredits);

        $em->flush();

        $this->addFlash(
            'success',
            sprintf(
                'Participation confirmée. %d crédit(s) utilisé(s). Il vous reste %d crédit(s).',
                $priceCredits,
                $user->getCredit()
            )
        );

        return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
    }

    /** Annuler une participation (remet places et recrédite avant départ). */
    #[Route(
        '/covoiturages/participations/{id}/annuler',
        name: 'covoiturage_annuler_participation',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_USER')]
    public function annulerParticipation(
        Request $request,
        Participation $participation,
        EntityManagerInterface $em
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            $this->addFlash('danger', 'Vous devez être connecté pour annuler une participation.');
            return $this->redirectToRoute('login');
        }

        if ($participation->getUtilisateur()?->getId() !== $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas annuler une participation qui ne vous appartient pas.');
            return $this->redirectToRoute('profile_rides');
        }

        if (!$this->isCsrfTokenValid(
            'annuler_participation_' . $participation->getId(),
            (string) $request->request->get('_token')
        )) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('profile_rides');
        }

        $covoiturage = $participation->getCovoiturage();
        if (!$covoiturage instanceof Covoiturage) {
            $this->addFlash('danger', 'Trajet introuvable pour cette participation.');
            return $this->redirectToRoute('profile_rides');
        }

        $dateDepart  = $covoiturage->getDateDepart();
        $heureDepart = $covoiturage->getHeureDepart();

        if ($dateDepart !== null) {
            $departStr      = $dateDepart->format('Y-m-d') . ' ' . ($heureDepart ? $heureDepart->format('H:i:s') : '00:00:00');
            $departDateTime = new \DateTimeImmutable($departStr);
            $now            = new \DateTimeImmutable('now');

            if ($now >= $departDateTime) {
                $this->addFlash(
                    'warning',
                    'Vous ne pouvez plus annuler cette participation car le trajet a déjà commencé (ou est passé).'
                );
                return $this->redirectToRoute('profile_rides');
            }
        }

        $refund = $this->participationManager->cancelParticipation($participation);

        $em->flush();

        $this->addFlash(
            'success',
            sprintf(
                'Votre participation a bien été annulée. %d crédit(s) vous ont été recrédités.',
                $refund
            )
        );

        return $this->redirectToRoute('profile_rides');
    }

    /** Confirmation par le passager (avis + crédit conducteur après validation). */
    #[Route(
        '/covoiturages/participations/{id}/confirmer',
        name: 'covoiturage_participation_confirmer',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_USER')]
    public function confirmer(
        Request $request,
        Participation $participation,
        EntityManagerInterface $em
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if ($participation->getUtilisateur()?->getId() !== $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez confirmer qu’un trajet auquel vous avez participé.');
            return $this->redirectToRoute('profile_rides');
        }

        if (!$this->isCsrfTokenValid(
            'confirmer_participation_' . $participation->getId(),
            (string) $request->request->get('_token')
        )) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('profile_rides');
        }

        $covoiturage = $participation->getCovoiturage();
        if (!$covoiturage instanceof Covoiturage) {
            $this->addFlash('danger', 'Trajet introuvable.');
            return $this->redirectToRoute('profile_rides');
        }

        if ($covoiturage->getStatut() !== 'TERMINE') {
            $this->addFlash('warning', 'Le conducteur doit marquer le trajet comme terminé avant votre confirmation.');
            return $this->redirectToRoute('profile_rides');
        }

        if ($participation->getConfirmationStatus() !== 'PENDING') {
            $this->addFlash('info', 'Vous avez déjà répondu pour ce trajet.');
            return $this->redirectToRoute('profile_feedback');
        }

        $comment = trim((string) $request->request->get('comment', ''));
        $comment = $comment !== '' ? mb_substr($comment, 0, 255) : null;
        $note    = $request->request->get('note');
        $note    = is_numeric($note) ? max(0, min(5, (int) $note)) : null;

        $participation
            ->setConfirmationStatus('AWAITING_VALIDATION')
            ->setConfirmationAt(new \DateTimeImmutable())
            ->setConfirmationComment($comment);

        $this->participationManager->upsertAvisForParticipation($participation, $comment, $note);

        $em->flush();

        $this->addFlash('success', 'Merci ! Votre confirmation a été prise en compte.');
        return $this->redirectToRoute('profile_rides');
    }

    /** Signalement d’un problème par le passager (avis à valider, pas de crédit). */
    #[Route(
        '/covoiturages/participations/{id}/signaler',
        name: 'covoiturage_participation_signaler',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_USER')]
    public function signaler(
        Request $request,
        Participation $participation,
        EntityManagerInterface $em
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if ($participation->getUtilisateur()?->getId() !== $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez signaler qu’un trajet auquel vous avez participé.');
            return $this->redirectToRoute('profile_rides');
        }

        if (!$this->isCsrfTokenValid(
            'signaler_participation_' . $participation->getId(),
            (string) $request->request->get('_token')
        )) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('profile_rides');
        }

        $covoiturage = $participation->getCovoiturage();
        if (!$covoiturage instanceof Covoiturage) {
            $this->addFlash('danger', 'Trajet introuvable.');
            return $this->redirectToRoute('profile_rides');
        }

        if ($covoiturage->getStatut() !== 'TERMINE') {
            $this->addFlash('warning', 'Le conducteur doit marquer le trajet comme terminé avant votre signalement.');
            return $this->redirectToRoute('profile_rides');
        }

        if ($participation->getConfirmationStatus() !== 'PENDING') {
            $this->addFlash('info', 'Vous avez déjà répondu pour ce trajet.');
            return $this->redirectToRoute('profile_feedback');
        }

        $comment = trim((string) $request->request->get('comment', ''));
        $comment = $comment !== '' ? mb_substr($comment, 0, 255) : null;
        $note    = $request->request->get('note');
        $note    = is_numeric($note) ? max(0, min(5, (int) $note)) : null;

        $participation
            ->setConfirmationStatus('REPORTED')
            ->setConfirmationComment($comment)
            ->setConfirmationAt(new \DateTimeImmutable());

        $this->participationManager->upsertAvisForParticipation($participation, $comment, $note);

        $em->flush();

        $this->addFlash(
            'info',
            'Votre signalement a été transmis. Un employé reviendra vers vous avant de créditer le conducteur.'
        );

        return $this->redirectToRoute('profile_rides');
    }
}
