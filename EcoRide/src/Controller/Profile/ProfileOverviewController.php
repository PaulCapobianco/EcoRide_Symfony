<?php

namespace App\Controller\Profile;

use App\Entity\Utilisateur;
use App\Repository\AvisRepository;
use App\Repository\CovoiturageRepository;
use App\Repository\VoitureRepository;
use App\Service\ProfileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Vue d'ensemble du profil utilisateur (crédits, stats, prefs, véhicules).
 */
class ProfileOverviewController extends AbstractController
{
    public function __construct(private ProfileHelper $profileHelper)
    {
    }

    /**
     * Tableau de bord utilisateur : crédits, stats, véhicules et avis paginés.
     */
    #[Route('/profil', name: 'profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        CovoiturageRepository $covoiturageRepository,
        AvisRepository $avisRepository,
        VoitureRepository $voitureRepository
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        $credits = $user->getCredit(); // Creds restants visibles dans l’entête
        $ridesCount = $covoiturageRepository->count(['utilisateur' => $user]);

        $ratingAvg   = 0.0;
        $ratingCount = 0;

        if ($user->getId() !== null) {
            $avgData     = $avisRepository->getAverageForDriver($user->getId());
            $ratingAvg   = $avgData['avg'];
            $ratingCount = $avgData['count'];
        }

        $vehicules = $voitureRepository->findBy(['utilisateur' => $user]); // affichage accordion
        $prefs     = $this->profileHelper->buildPreferencesForUser($user);

        $avisPerPage = 5;
        $avisPage    = max(1, (int) $request->query->get('avis_page', 1));
        $totalAvis   = ($user->getId() !== null) ? $avisRepository->countForDriver($user->getId()) : 0;
        $totalPages  = max(1, (int) ceil(max($totalAvis, 1) / $avisPerPage));
        $avisPage    = min($avisPage, $totalPages);
        $offset      = ($avisPage - 1) * $avisPerPage;

        $driverAvisItems = [];
        if ($totalAvis > 0 && $user->getId() !== null) {
            $driverAvisItems = $avisRepository->findForDriverPaginated($user->getId(), $avisPerPage, $offset);
        }

        // Pager transmis au template pour les avis conducteur
        $driverAvisPagination = [
            'items' => $driverAvisItems,
            'page'  => $avisPage,
            'pages' => $totalPages,
            'total' => $totalAvis,
            'limit' => $avisPerPage,
        ];

        return $this->render('profile/profile.html.twig', [
            'user'                     => $user,
            'credits'                  => $credits,
            'rides_count'              => $ridesCount,
            'rating_avg'               => $ratingAvg,
            'rating_count'             => $ratingCount,
            'vehicules'                => $vehicules,
            'prefs'                    => $prefs,
            'driver_avis'              => $driverAvisItems,
            'driver_avis_pagination'   => $driverAvisPagination,
        ]);
    }
}
