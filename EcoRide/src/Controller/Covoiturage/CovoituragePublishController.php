<?php

namespace App\Controller\Covoiturage;

use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Form\PublishRideType;
use App\Service\RideManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Publication et édition de covoiturages par le conducteur.
 */
class CovoituragePublishController extends AbstractController
{
    public function __construct(private RideManager $rideManager)
    {
    }

    /**
     * Publier un trajet :
     * - GET : affiche le formulaire avec les véhicules du conducteur
     * - POST : crée un Covoiturage en BDD si tout est valide
     * Montants en crédits.
     */
    #[Route('/covoiturages/publier', name: 'covoiturage_publish', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function publish(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            $this->addFlash('danger', 'Vous devez être connecté pour publier un trajet.');
            return $this->redirectToRoute('home');
        }

        // 🔒 Seuls les profils "driver" ou "both" peuvent publier
        if (!$user->isDriverProfile()) {
            $this->addFlash(
                'warning',
                'Vous devez activer le mode conducteur dans votre profil pour pouvoir publier un trajet.'
            );

            return $this->redirect($this->generateUrl('profile') . '#section-role');
        }

        $vehicules = $this->rideManager->getDriverVehicles($user);

        if (empty($vehicules)) {
            $this->addFlash(
                'warning',
                'Vous devez d’abord ajouter au moins un véhicule dans votre profil avant de pouvoir publier un trajet.'
            );

            return $this->redirectToRoute('profile_vehicle_new');
        }

        $vehicleHelp = empty($vehicules)
            ? 'Aucun véhicule enregistré. Ajoutez d’abord un véhicule dans votre profil.'
            : 'Les véhicules proviennent de votre profil.';

        $form = $this->createForm(PublishRideType::class, null, [
            'vehicules'    => $vehicules,
            'vehicle_help' => $vehicleHelp,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string,mixed> $data */
            $data  = $form->getData();
            $ride  = new Covoiturage();

            $this->rideManager->hydrateRideFromForm($ride, $data, $user);

            $em->persist($ride);
            $em->flush();

            $this->addFlash('success', 'Votre trajet a bien été publié.');

            return $this->redirectToRoute('covoiturage_detail', [
                'id' => $ride->getId(),
            ]);
        }

        return $this->render('covoiturage/publish.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Modifier un trajet existant (CRUD - update).
     */
    #[Route('/covoiturages/{id}/modifier', name: 'covoiturage_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function edit(
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
            $this->addFlash('danger', 'Vous ne pouvez pas modifier ce trajet.');
            return $this->redirectToRoute('profile_rides');
        }

        $vehicules = $this->rideManager->getDriverVehicles($user);

        $vehicleHelp = empty($vehicules)
            ? 'Aucun véhicule enregistré. Ajoutez d’abord un véhicule dans votre profil.'
            : 'Les véhicules proviennent de votre profil.';

        $initialData = [
            'from'        => $covoiturage->getLieuDepart(),
            'fromAddress' => $covoiturage->getAdresseDepart(),
            'to'          => $covoiturage->getLieuArrivee(),
            'toAddress'   => $covoiturage->getAdresseArrivee(),
            'date'        => $covoiturage->getDateDepart(),
            'timeStart'   => $covoiturage->getHeureDepart(),
            'timeEnd'     => $covoiturage->getHeureArrivee(),
            'vehicle'     => $covoiturage->getVoiture(),
            'seats'       => $covoiturage->getNbPlace(),
            'price'       => $covoiturage->getPrixPersonne(),
            'terms'       => true,
        ];

        $form = $this->createForm(PublishRideType::class, $initialData, [
            'vehicules'    => $vehicules,
            'vehicle_help' => $vehicleHelp,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string,mixed> $data */
            $data = $form->getData();

            $this->rideManager->hydrateRideFromForm($covoiturage, $data, $user);

            $em->flush();

            $this->addFlash('success', 'Votre trajet a été mis à jour.');
            return $this->redirectToRoute('profile_rides');
        }

        return $this->render('covoiturage/edit.html.twig', [
            'form'        => $form->createView(),
            'covoiturage' => $covoiturage,
        ]);
    }
}
