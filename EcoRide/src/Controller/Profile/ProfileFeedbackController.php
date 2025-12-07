<?php

namespace App\Controller\Profile;

use App\Entity\Participation;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Page des avis/confirmations de trajets pour le passager.
 */
class ProfileFeedbackController extends AbstractController
{
    /**
     * Liste des trajets terminés qui attendent un feedback utilisateur.
     */
    #[Route('/profil/avis-trajets', name: 'profile_feedback', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        $participations = $em->getRepository(Participation::class)
            ->createQueryBuilder('p')
            ->innerJoin('p.covoiturage', 'c')
            ->addSelect('c')
            ->andWhere('p.utilisateur = :user')
            ->andWhere('c.statut = :termine')
            ->andWhere('(p.confirmationStatus = :pending OR p.confirmationStatus IS NULL)')
            ->setParameter('user', $user)
            ->setParameter('termine', 'TERMINE')
            ->setParameter('pending', 'PENDING')
            ->orderBy('c.dateDepart', 'DESC')
            ->addOrderBy('c.heureDepart', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('profile/feedback.html.twig', [
            'participations' => $participations,
        ]);
    }
}
