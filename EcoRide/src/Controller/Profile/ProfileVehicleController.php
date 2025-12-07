<?php

namespace App\Controller\Profile;

use App\Entity\Voiture;
use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Form\VehiculeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion des véhicules du profil (CRUD).
 */
#[Route('/profile/vehicules', name: 'profile_vehicle_')]
#[IsGranted('ROLE_USER')]
class ProfileVehicleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * Ajoute un véhicule au profil (et active le rôle conducteur si demandé).
     */
    #[Route('/nouveau', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $vehicule = new Voiture();

        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        $targetRole = (string) $request->query->get('target_role', '');

        if (method_exists($vehicule, 'setUtilisateur')) {
            $vehicule->setUtilisateur($user);
        }

        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($vehicule);

            if (in_array($targetRole, ['driver', 'both'], true)) {
                $user->setProfilType($targetRole);
            }

            $this->em->flush();

            $this->addFlash('success', 'Véhicule ajouté à votre profil.');

            if (in_array($targetRole, ['driver', 'both'], true)) {
                return $this->redirectToRoute('profile', [
                    '_fragment' => 'section-role',
                ]);
            }

            return $this->redirectToRoute('profile', [
                '_fragment' => 'section-vehicules',
            ]);
        }

        return $this->render('profile/vehicule.html.twig', [
            'form'     => $form,
            'vehicule' => $vehicule,
            'is_edit'  => false,
        ]);
    }

    /**
     * Modifie un véhicule existant.
     */
    #[Route('/{id}/modifier', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Voiture $vehicule, Request $request): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if (method_exists($vehicule, 'getUtilisateur') && $vehicule->getUtilisateur() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce véhicule.');
        }

        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->flush();

            $this->addFlash('success', 'Véhicule mis à jour.');

            return $this->redirectToRoute('profile', [
                '_fragment' => 'section-vehicules',
            ]);
        }

        return $this->render('profile/vehicule.html.twig', [
            'form'     => $form,
            'vehicule' => $vehicule,
            'is_edit'  => true,
        ]);
    }

    /**
     * Supprime un véhicule si aucun trajet ne l’utilise.
     */
    #[Route('/{id}/supprimer', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Voiture $vehicule, Request $request): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if (method_exists($vehicule, 'getUtilisateur') && $vehicule->getUtilisateur() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce véhicule.');
        }

        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_vehicle_'.$vehicule->getId(), $submittedToken)) {
            $this->addFlash('danger', 'Token CSRF invalide.');

            return $this->redirectToRoute('profile', [
                '_fragment' => 'section-vehicules',
            ]);
        }

        $rideCount = $this->em->getRepository(Covoiturage::class)
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.voiture = :voiture')
            ->setParameter('voiture', $vehicule)
            ->getQuery()
            ->getSingleScalarResult();

        if ($rideCount > 0) {
            $this->addFlash(
                'danger',
                "Impossible de supprimer ce véhicule : il est utilisé dans {$rideCount} covoiturage(s). "
                . "Modifiez ou supprimez d'abord ces trajets."
            );

            return $this->redirectToRoute('profile', [
                '_fragment' => 'section-vehicules',
            ]);
        }

        $this->em->remove($vehicule);
        $this->em->flush();

        $this->addFlash('success', 'Véhicule supprimé.');

        return $this->redirectToRoute('profile', [
            '_fragment' => 'section-vehicules',
        ]);
    }
}
