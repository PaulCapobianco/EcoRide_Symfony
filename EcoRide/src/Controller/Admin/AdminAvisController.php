<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Espace admin/employé : validation/refus d'avis et suivi des incidents.
 */
class AdminAvisController extends AbstractController
{
    /**
     * Tableau de suivi des avis : en attente, validés, refusés (paginations séparées).
     */
    #[Route('/admin/avis', name: 'admin_avis_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException();
        }

        $avisRepo = $em->getRepository(Avis::class);

        $perPage = 5;
        $pendingPage   = max(1, (int) $request->query->get('pending_page', 1));
        $validatedPage = max(1, (int) $request->query->get('validated_page', 1));
        $refusedPage   = max(1, (int) $request->query->get('refused_page', 1));

        // Récupération des avis à valider (pagination par cartes)
        $pendingQb = $avisRepo
            ->createQueryBuilder('a')
            ->leftJoin('a.covoiturage', 'c')
            ->addSelect('c')
            ->leftJoin('a.utilisateur', 'u')
            ->addSelect('u')
            ->where('a.statut = :statut')
            ->setParameter('statut', 'A_VALIDER')
            ->orderBy('a.id', 'DESC');

        $pendingTotal = (clone $pendingQb)
            ->select('COUNT(a.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();

        $pendingAvis = $pendingQb
            ->setFirstResult(($pendingPage - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        // Historique des avis validés
        $validatedQb = $avisRepo
            ->createQueryBuilder('a')
            ->leftJoin('a.covoiturage', 'c')
            ->addSelect('c')
            ->leftJoin('a.utilisateur', 'u')
            ->addSelect('u')
            ->where('a.statut = :statut')
            ->setParameter('statut', 'VALIDE')
            ->orderBy('a.id', 'DESC');

        $validatedTotal = (clone $validatedQb)
            ->select('COUNT(a.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();

        $validatedAvis = $validatedQb
            ->setFirstResult(($validatedPage - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        // Historique des refus (incidents clos)
        $refusedQb = $avisRepo
            ->createQueryBuilder('a')
            ->leftJoin('a.covoiturage', 'c')
            ->addSelect('c')
            ->leftJoin('a.utilisateur', 'u')
            ->addSelect('u')
            ->where('a.statut = :statut')
            ->setParameter('statut', 'REFUSE')
            ->orderBy('a.id', 'DESC');

        $refusedTotal = (clone $refusedQb)
            ->select('COUNT(a.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();

        $refusedAvis = $refusedQb
            ->setFirstResult(($refusedPage - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return $this->render('admin/avis/avis.html.twig', [
            'pending_avis' => $pendingAvis,
            'validated_avis' => $validatedAvis,
            'refused_avis' => $refusedAvis,
            'pending_page' => $pendingPage,
            'pending_pages' => (int) ceil($pendingTotal / $perPage),
            'validated_page' => $validatedPage,
            'validated_pages' => (int) ceil($validatedTotal / $perPage),
            'refused_page' => $refusedPage,
            'refused_pages' => (int) ceil($refusedTotal / $perPage),
        ]);
    }

    /**
     * Validation manuelle d’un avis et crédit du conducteur (si non déjà payé).
     */
    #[Route('/admin/avis/{id}/valider', name: 'admin_avis_valider', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function valider(
        Avis $avis,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('valider_avis_' . $avis->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('admin_avis_index');
        }

        if ($avis->getStatut() !== 'A_VALIDER') {
            $this->addFlash('info', 'Cet avis a déjà été traité.');
            return $this->redirectToRoute('admin_avis_index');
        }

        $participation = null;
        $covoiturage   = $avis->getCovoiturage();
        $passenger     = $avis->getUtilisateur();

        if ($covoiturage && $passenger) {
            $participation = $em->getRepository(Participation::class)->findOneBy([
                'covoiturage'  => $covoiturage,
                'utilisateur' => $passenger,
            ]);
        }

        // Créditer le conducteur si besoin
        if ($participation && $covoiturage) {
            $driver = $covoiturage->getUtilisateur();
            if ($driver && $participation->getConfirmationStatus() !== 'VALIDATED') {
                $nbPlaces = (int) ($participation->getNbPlaces() ?? 1);
                $gain     = ((int) $covoiturage->getPrixPersonne()) * $nbPlaces;
                $driver->setCredit($driver->getCredit() + $gain);

                $participation
                    ->setConfirmationStatus('VALIDATED')
                    ->setConfirmationAt(new \DateTimeImmutable());
            }
        }

        $avis->setStatut('VALIDE');
        $em->flush();

        $this->addFlash('success', 'Avis validé et conducteur crédité le cas échéant.');
        return $this->redirectToRoute('admin_avis_index');
    }

    /**
     * Refus d’un avis signalé (le passager indique un problème à traiter offline).
     */
    #[Route('/admin/avis/{id}/refuser', name: 'admin_avis_refuser', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function refuser(
        Avis $avis,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('refuser_avis_' . $avis->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('admin_avis_index');
        }

        if ($avis->getStatut() !== 'A_VALIDER') {
            $this->addFlash('info', 'Cet avis a déjà été traité.');
            return $this->redirectToRoute('admin_avis_index');
        }

        $participation = null;
        $covoiturage   = $avis->getCovoiturage();
        $passenger     = $avis->getUtilisateur();

        if ($covoiturage && $passenger) {
            $participation = $em->getRepository(Participation::class)->findOneBy([
                'covoiturage'  => $covoiturage,
                'utilisateur' => $passenger,
            ]);
        }

        if ($participation) {
            $participation
                ->setConfirmationStatus('REFUSED')
                ->setConfirmationAt(new \DateTimeImmutable());
        }

        $avis->setStatut('REFUSE');
        $em->flush();

        $this->addFlash('info', 'Avis refusé. Aucun crédit n’a été versé.');
        return $this->redirectToRoute('admin_avis_index');
    }

    /**
     * Liste des participations signalées (statut REPORTED).
     */
    #[Route('/admin/incidents', name: 'admin_incidents', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function incidents(EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException();
        }

        $reported = $em->getRepository(Participation::class)
            ->createQueryBuilder('p')
            ->innerJoin('p.covoiturage', 'c')
            ->addSelect('c')
            ->innerJoin('p.utilisateur', 'u')
            ->addSelect('u')
            ->innerJoin('c.utilisateur', 'driver')
            ->addSelect('driver')
            ->andWhere('p.confirmationStatus = :status')
            ->setParameter('status', 'REPORTED')
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/avis/incidents.html.twig', [
            'reported_participations' => $reported,
        ]);
    }

    /**
     * Crédite le passager suite à un litige résolu.
     */
    #[Route('/admin/incidents/{id}/credit', name: 'admin_incident_credit', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function creditPassenger(
        Participation $participation,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException();
        }

        if (
            !$this->isCsrfTokenValid(
                'credit_incident_' . $participation->getId(),
                (string) $request->request->get('_token')
            )
        ) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('admin_incidents');
        }

        $amount = (int) $request->request->get('amount', 0);
        if ($amount <= 0) {
            $this->addFlash('danger', 'Montant de crédit invalide.');
            return $this->redirectToRoute('admin_incidents');
        }

        $passenger = $participation->getUtilisateur();
        if (!$passenger) {
            $this->addFlash('danger', 'Impossible de retrouver le passager.');
            return $this->redirectToRoute('admin_incidents');
        }

        $passenger->setCredit($passenger->getCredit() + $amount);

        $reason = trim((string) $request->request->get('reason', ''));
        if ($reason !== '') {
            $note = '[Crédit manuel] ' . $reason;
            $existingComment = $participation->getConfirmationComment();
            $participation->setConfirmationComment(
                $existingComment ? $existingComment . ' | ' . $note : $note
            );
        }

        $participation
            ->setConfirmationStatus('RESOLVED')
            ->setConfirmationAt(new \DateTimeImmutable());

        $em->flush();

        $this->addFlash('success', sprintf(
            'Crédit de %d accordé à %s.',
            $amount,
            $passenger->getEmail()
        ));

        return $this->redirectToRoute('admin_incidents');
    }
}
