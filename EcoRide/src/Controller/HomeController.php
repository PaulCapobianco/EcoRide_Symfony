<?php

namespace App\Controller;

use App\Repository\CovoiturageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Page d'accueil avec les derniers trajets proposés.
 */
class HomeController extends AbstractController
{
    /**
     * Page d’accueil affichant les trois derniers trajets proposés.
     */
    #[Route('/', name: 'home')]
    public function index(CovoiturageRepository $covoiturageRepository): Response
    {
        $lastRides = $covoiturageRepository->findLastRides(3);

        return $this->render('home/home.html.twig', [
            'lastRides' => $lastRides,
        ]);
    }
}
