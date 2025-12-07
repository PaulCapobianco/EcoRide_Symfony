<?php

namespace App\Controller\Profile;

use App\Entity\Covoiturage;
use App\Entity\Participation;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Liste des trajets de l'utilisateur (conducteur et passager).
 */
final class ProfileRidesController extends AbstractController
{
    /**
     * Affiche les trajets du profil (conducteur & passager) avec pagination tabulaire.
     */
    #[Route('/profile/rides', name: 'profile_rides', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        $ridesAsDriver = $em->getRepository(Covoiturage::class)
            ->createQueryBuilder('c')
            ->andWhere('c.utilisateur = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateDepart', 'DESC')
            ->addOrderBy('c.heureDepart', 'DESC')
            ->getQuery()
            ->getResult();

        $participations = $em->getRepository(Participation::class)
            ->createQueryBuilder('p')
            ->innerJoin('p.covoiturage', 'c')
            ->addSelect('c')
            ->andWhere('p.utilisateur = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateDepart', 'DESC')
            ->addOrderBy('c.heureDepart', 'DESC')
            ->getQuery()
            ->getResult();

        $driverActive = [];
        $driverHistory = [];
        foreach ($ridesAsDriver as $ride) {
            $status = $ride->getStatut() ?? 'OUVERT';
            if (in_array($status, ['TERMINE', 'ANNULE'], true)) {
                $driverHistory[] = $ride;
            } else {
                $driverActive[] = $ride;
            }
        }

        $passengerActive = [];
        $passengerHistory = [];
        foreach ($participations as $participation) {
            $ride = $participation->getCovoiturage();
            if (!$ride) {
                continue;
            }
            $status = $ride->getStatut() ?? 'OUVERT';
            if (in_array($status, ['TERMINE', 'ANNULE'], true)) {
                $passengerHistory[] = $participation;
            } else {
                $passengerActive[] = $participation;
            }
        }

        $tab = $request->query->get('tab', 'current');
        if (!in_array($tab, ['current', 'history'], true)) {
            $tab = 'current';
        }

        $driverCollection = $tab === 'history' ? $driverHistory : $driverActive;
        $passengerCollection = $tab === 'history' ? $passengerHistory : $passengerActive;

        $driverPage = max(1, (int) $request->query->get('driver_page', 1));
        $passengerPage = max(1, (int) $request->query->get('passenger_page', 1));

        $driverPagination = $this->paginateArray($driverCollection, $driverPage);
        $passengerPagination = $this->paginateArray($passengerCollection, $passengerPage);

        return $this->render('profile/rides.html.twig', [
            'user'                    => $user,
            'rides_as_driver_active'  => $driverActive,
            'rides_as_driver_history' => $driverHistory,
            'participations_active'   => $passengerActive,
            'participations_history'  => $passengerHistory,
            'active_tab'              => $tab,
            'driver_pagination'       => $driverPagination,
            'passenger_pagination'    => $passengerPagination,
        ]);
    }

    /**
     * Paginate an array-based collection.
     *
     * @return array{items: array, page: int, pages: int, total: int, limit: int}
     */
    private function paginateArray(array $items, int $page, int $limit = 5): array
    {
        $total = count($items);
        $pages = max(1, (int) ceil($total / $limit));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $limit;

        return [
            'items' => array_slice($items, $offset, $limit),
            'page'  => $page,
            'pages' => $pages,
            'total' => $total,
            'limit' => $limit,
        ];
    }
}
