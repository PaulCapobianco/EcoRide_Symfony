<?php

namespace App\Controller\Covoiturage;

use App\Entity\Covoiturage;
use App\Entity\Configuration;
use App\Entity\Parametre;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use App\Repository\CovoiturageRepository;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Recherche et détail des covoiturages publics.
 */
class CovoiturageController extends AbstractController
{
    /**
     * Page de recherche des covoiturages (filtres + pagination).
     */
    #[Route('/covoiturages', name: 'covoiturages', methods: ['GET'])]
    public function index(
        Request $request,
        CovoiturageRepository $covoiturageRepository,
        AvisRepository $avisRepository
    ): Response {
        $fromRaw       = $request->query->get('from', '');
        $toRaw         = $request->query->get('to', '');
        $dateRaw       = $request->query->get('date', '');
        $ecoRaw        = $request->query->get('eco');
        $priceMaxRaw   = $request->query->get('price_max', '');
        $durationRaw   = $request->query->get('duration_max', '');
        $ratingMinRaw  = $request->query->get('rating_min', '');

        $from = trim((string) $fromRaw);
        $to   = trim((string) $toRaw);
        $date = $dateRaw !== '' ? (string) $dateRaw : null;

        $filtersForRepo = [
            'from'         => $from !== '' ? $from : null,
            'to'           => $to !== '' ? $to : null,
            'date'         => $date,
            'eco'          => $ecoRaw !== null ? 1 : null,
            'price_max'    => ($priceMaxRaw !== '' && $priceMaxRaw !== null) ? (float) $priceMaxRaw : null,
            'duration_max' => ($durationRaw !== '' && $durationRaw !== null) ? (int) $durationRaw : null,
            'rating_min'   => ($ratingMinRaw !== '' && $ratingMinRaw !== null) ? (float) $ratingMinRaw : null,
        ];

        // Copie pour la vue (checkbox eco booléenne, etc.)
        $filtersForView = [
            'from'         => $filtersForRepo['from'],
            'to'           => $filtersForRepo['to'],
            'date'         => $filtersForRepo['date'],
            'eco'          => $filtersForRepo['eco'] ? true : false,
            'price_max'    => $filtersForRepo['price_max'],
            'duration_max' => $filtersForRepo['duration_max'],
            'rating_min'   => $filtersForRepo['rating_min'],
        ];

        $hasMainSearch = $filtersForRepo['from'] || $filtersForRepo['to'] || $filtersForRepo['date'];

        if (!$hasMainSearch) {
            return $this->render('covoiturage/covoiturage.html.twig', [
                'rides'                => [],
                'filters'              => $filtersForView,
                'page'                 => 1,
                'pages'                => 1,
                'total_pages'          => 1,
                'total'                => 0,
                'ratings'              => [],
                'next_date_suggestion' => null,
            ]);
        }

        $page  = max(1, (int) $request->query->get('page', 1));
        $limit = 5;

        $result = $covoiturageRepository->searchWithFiltersAndPagination($filtersForRepo, $page, $limit);

        $rides = $result['items'];
        $total = $result['total'];
        $pages = $result['pages'];
        $page  = $result['page'];

        $driverIds = [];
        foreach ($rides as $ride) {
            $driver = $ride->getUtilisateur();
            if ($driver !== null && $driver->getId() !== null) {
                $driverIds[] = $driver->getId();
            }
        }
        $driverIds = array_values(array_unique($driverIds));

        $ratings = [];
        if (!empty($driverIds)) {
            $ratings = $avisRepository->getAverageRatingsByUser($driverIds);
        }

        $nextDateSuggestion = null;

        if ($total === 0 && $filtersForRepo['date'] !== null) {
            // Si aucune offre le jour demandé, on propose le prochain jour disponible
            try {
                $searchDate = new \DateTimeImmutable($filtersForRepo['date']);

                $nextFilters = $filtersForRepo;
                $nextFilters['date'] = $searchDate->modify('+1 day')->format('Y-m-d');

                $nextResult = $covoiturageRepository->searchWithFiltersAndPagination($nextFilters, 1, 1);

                if ($nextResult['total'] > 0 && !empty($nextResult['items'])) {
                    /** @var Covoiturage $firstRide */
                    $firstRide = $nextResult['items'][0];
                    $nextDateSuggestion = $firstRide->getDateDepart();
                }
            } catch (\Exception $e) {
                $nextDateSuggestion = null;
            }
        }

        return $this->render('covoiturage/covoiturage.html.twig', [
            'rides'                => $rides,
            'filters'              => $filtersForView,
            'page'                 => $page,
            'pages'                => $pages,
            'total_pages'          => $pages,
            'total'                => $total,
            'ratings'              => $ratings,
            'next_date_suggestion' => $nextDateSuggestion,
        ]);
    }

    /** Page détail d’un covoiturage : infos conducteur, voiture, avis, etc. */
    #[Route('/covoiturages/{id}', name: 'covoiturage_detail', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function detail(
        Covoiturage $covoiturage,
        AvisRepository $avisRepository,
        EntityManagerInterface $em
    ): Response {
        $user    = $this->getUser();
        $driver  = $covoiturage->getUtilisateur();
        $voiture = $covoiturage->getVoiture();

        $durationText = null;
        if ($covoiturage->getHeureArrivee() !== null) {
            $departStr  = $covoiturage->getDateDepart()->format('Y-m-d') . ' ' . $covoiturage->getHeureDepart()->format('H:i:s');
            $arriveeStr = $covoiturage->getDateArrivee()->format('Y-m-d') . ' ' . $covoiturage->getHeureArrivee()->format('H:i:s');

            $departDateTime  = new \DateTimeImmutable($departStr);
            $arriveeDateTime = new \DateTimeImmutable($arriveeStr);

            $interval     = $departDateTime->diff($arriveeDateTime);
            $totalHours   = $interval->days * 24 + $interval->h;
            $minutes      = $interval->i;
            $durationText = sprintf('%dh%02d', $totalHours, $minutes);
        }

        $placesRestantes = $covoiturage->getPlacesRestantes();
        $priceCredits    = (int) $covoiturage->getPrixPersonne();

        $isEco = false;
        if ($voiture !== null) {
            $energie     = $voiture->getEnergie();
            $ecoEnergies = ['Électrique', 'Electrique'];
            $isEco       = in_array($energie, $ecoEnergies, true);
        }

        $ratingAvg   = null;
        $ratingCount = 0;

        if ($driver !== null && $driver->getId() !== null) {
            $map = $avisRepository->getAverageRatingsByUser([$driver->getId()]);
            if (isset($map[$driver->getId()])) {
                $ratingAvg   = $map[$driver->getId()]['avg'];
                $ratingCount = $map[$driver->getId()]['count'];
            }
        }

        $avisList = [];
        if ($driver instanceof Utilisateur) {
            $avisList = $avisRepository->findBy(
                ['utilisateur' => $driver],
                ['id' => 'DESC']
            );
        }

        $preferences = [];

        if ($driver instanceof Utilisateur) {
            $configRepo = $em->getRepository(Configuration::class);
            $paramRepo  = $em->getRepository(Parametre::class);

            /** @var Configuration|null $configuration */
            $configuration = $configRepo->findOneBy(['utilisateur' => $driver]);

            if ($configuration) {
                /** @var Parametre[] $params */
                $params = $paramRepo->findBy(
                    ['configuration' => $configuration],
                    ['id' => 'ASC']
                );

                $builtinLabels = [
                    'music'     => 'Musique',
                    'ac'        => 'Climatisation',
                    'pets'      => 'Animaux',
                    'baggage'   => 'Bagages volumineux',
                    'noSmoking' => 'Non-fumeur',
                ];

                $enabledValues = ['1', 'oui', 'true', 'on', 'yes'];

                foreach ($params as $param) {
                    $prop  = $param->getPropriete();
                    $value = (string) $param->getValeur();

                    if ($prop === 'custom') {
                        $label = trim($value);
                        if ($label !== '') {
                            $preferences[] = [
                                'code'    => 'custom',
                                'label'   => $label,
                                'enabled' => true,
                            ];
                        }
                        continue;
                    }

                    if (!isset($builtinLabels[$prop])) {
                        continue;
                    }

                    $normalizedValue = strtolower(trim($value));

                    if (!in_array($normalizedValue, $enabledValues, true)) {
                        continue;
                    }

                    $preferences[] = [
                        'code'    => $prop,
                        'label'   => $builtinLabels[$prop],
                        'enabled' => true,
                    ];
                }
            }
        }

        $userCredit          = null;
        $canParticipate      = false;
        $participationReason = null;

        if ($user instanceof Utilisateur) {
            $userCredit = $user->getCredit();
            $isDriver   = $driver && $driver->getId() === $user->getId();
            $profilType = $user->getProfilType();

            if ($placesRestantes <= 0) {
                $participationReason = 'Ce trajet est complet.';
            } elseif ($isDriver) {
                $participationReason = 'Vous êtes le conducteur de ce trajet.';
            } elseif ($profilType === 'driver') {
                $participationReason = 'Votre profil est configuré comme conducteur uniquement. Activez le mode passager ou "les deux" dans votre profil pour participer à un trajet.';
            } elseif ($userCredit < $priceCredits) {
                $participationReason = 'Crédits insuffisants pour participer.';
            } else {
                $canParticipate = true;
            }
        }

        return $this->render('covoiturage/detail.html.twig', [
            'covoiturage'          => $covoiturage,
            'driver'               => $driver,
            'voiture'              => $voiture,
            'duration_text'        => $durationText,
            'places_restantes'     => $placesRestantes,
            'price_credits'        => $priceCredits,
            'is_eco'               => $isEco,
            'rating_avg'           => $ratingAvg,
            'rating_count'         => $ratingCount,
            'user_credit'          => $userCredit,
            'can_participate'      => $canParticipate,
            'participation_reason' => $participationReason,
            'preferences'          => $preferences,
            'avis_list'            => $avisList,
        ]);
    }
}
