<?php

namespace App\Controller\Covoiturage;

use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Service\RideManager;
use App\Service\RideNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Cycle de vie d’un covoiturage : démarrer, terminer, annuler, supprimer.
 */
class CovoiturageLifecycleController extends AbstractController
{
    public function __construct(
        private RideManager $rideManager,
        private RideNotificationService $rideNotification
    ) {
    }

    /** Démarrer un covoiturage (statut EN_COURS). */
    #[Route(
        '/covoiturages/{id}/demarrer',
        name: 'covoiturage_start',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_USER')]
    public function start(
        Covoiturage $covoiturage,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if ($covoiturage->getUtilisateur()?->getId() !== $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas démarrer un trajet qui ne vous appartient pas.');
            return $this->redirectToRoute('profile_rides');
        }

        if (!$this->isCsrfTokenValid('start_ride_' . $covoiturage->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('profile_rides');
        }

        if (!in_array($covoiturage->getStatut(), ['OUVERT', 'COMPLET'], true)) {
            $this->addFlash('warning', 'Ce trajet ne peut pas être démarré dans son état actuel.');
            return $this->redirectToRoute('profile_rides');
        }

        $covoiturage
            ->setStatut('EN_COURS')
            ->setStartedAt(new \DateTimeImmutable());

        $em->flush();

        $this->addFlash('success', 'Trajet démarré. Bon trajet !');
        return $this->redirectToRoute('profile_rides');
    }

    /** Terminer un covoiturage et notifier les passagers. */
    #[Route(
        '/covoiturages/{id}/terminer',
        name: 'covoiturage_finish',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_USER')]
    public function finish(
        Covoiturage $covoiturage,
        Request $request,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if ($covoiturage->getUtilisateur()?->getId() !== $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas clôturer un trajet qui ne vous appartient pas.');
            return $this->redirectToRoute('profile_rides');
        }

        if (!$this->isCsrfTokenValid('finish_ride_' . $covoiturage->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('profile_rides');
        }

        if ($covoiturage->getStatut() !== 'EN_COURS') {
            $this->addFlash('warning', 'Ce trajet doit d’abord être démarré pour être clôturé.');
            return $this->redirectToRoute('profile_rides');
        }

        $covoiturage
            ->setStatut('TERMINE')
            ->setFinishedAt(new \DateTimeImmutable());

        $feedbackUrl = $urlGenerator->generate('profile_feedback', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->rideNotification->notifyFeedbackRequests($covoiturage, $feedbackUrl);

        $em->flush();

        $this->addFlash(
            'success',
            'Trajet marqué comme terminé. Les passagers ont été invités à confirmer le bon déroulement.'
        );

        return $this->redirectToRoute('profile_rides');
    }

    /** Supprimer un covoiturage. */
    #[Route(
        '/covoiturages/{id}/supprimer',
        name: 'covoiturage_delete',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_USER')]
    public function delete(
        Covoiturage $covoiturage,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if ($covoiturage->getUtilisateur()?->getId() !== $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer ce trajet.');
            return $this->redirectToRoute('profile_rides');
        }

        if (!$this->isCsrfTokenValid('delete_ride_' . $covoiturage->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('profile_rides');
        }

        $em->remove($covoiturage);
        $em->flush();

        $this->addFlash('success', 'Le trajet a bien été supprimé.');
        return $this->redirectToRoute('profile_rides');
    }

    /** Annulation par le conducteur (rembourse passagers et notifie). */
    #[Route(
        '/covoiturages/{id}/annuler',
        name: 'covoiturage_cancel_driver',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_USER')]
    public function cancelByDriver(
        Covoiturage $covoiturage,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if ($covoiturage->getUtilisateur()?->getId() !== $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas annuler ce trajet.');
            return $this->redirectToRoute('profile_rides');
        }

        if (!$this->isCsrfTokenValid(
            'cancel_ride_' . $covoiturage->getId(),
            (string) $request->request->get('_token')
        )) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
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
                    'Vous ne pouvez plus annuler ce trajet car il a déjà commencé (ou est passé).'
                );
                return $this->redirectToRoute('profile_rides');
            }
        }

        $this->rideManager->refundPassengers($covoiturage);
        $this->rideNotification->notifyCancellation($covoiturage);

        $covoiturage->setStatut('ANNULE');
        $covoiturage->setNbPlace(0);

        $em->flush();

        $this->addFlash(
            'success',
            'Le trajet a été annulé. Tous les participants ont été remboursés et notifiés par e-mail.'
        );

        return $this->redirectToRoute('profile_rides');
    }
}
