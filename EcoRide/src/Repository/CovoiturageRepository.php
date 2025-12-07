<?php

namespace App\Repository;

use App\Entity\Covoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Requêtes de recherche côté front pour les covoiturages (filters + pagination).
 * @extends ServiceEntityRepository<Covoiturage>
 */
class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

    /**
     * Méthode centrale :
     * - applique les filtres (from / to / date / éco / prix / note)
     * - filtre la durée max côté PHP
     * - NE GARDE QUE les trajets avec au moins 1 place dispo
     * - gère la pagination (page / limit)
     *
     * @param array $filters [
     *   from         => ?string,     // ville ou adresse de départ
     *   to           => ?string,     // ville ou adresse d’arrivée
     *   date         => ?string,     // Y-m-d
     *   eco          => ?bool|int,   // 1 pour éco uniquement
     *   price_max    => ?float,
     *   duration_max => ?int,        // heures
     *   rating_min   => ?float       // note minimale du chauffeur
     * ]
     * @param int $page
     * @param int $limit
     *
     * @return array{
     *   items: Covoiturage[],
     *   total: int,
     *   page: int,
     *   pages: int,
     *   limit: int
     * }
     */
    public function searchWithFiltersAndPagination(array $filters, int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')
            ->addSelect('v')
            ->leftJoin('c.utilisateur', 'u')
            ->addSelect('u')
            // On ne liste que les trajets encore "ouverts" ou "complets" (pas terminés/annulés/en cours)
            ->andWhere('(c.statut IS NULL OR c.statut = :openStatus)')
            ->setParameter('openStatus', 'OUVERT')
            ->orderBy('c.dateDepart', 'ASC')
            ->addOrderBy('c.heureDepart', 'ASC');

        // --- Filtres simples ---

        // Départ : ville + adresse précise
        if (!empty($filters['from'])) {
            $qb
                ->andWhere('(c.lieuDepart LIKE :from OR c.adresseDepart LIKE :from)')
                ->setParameter('from', '%' . $filters['from'] . '%');
        }

        // Arrivée : ville + adresse précise
        if (!empty($filters['to'])) {
            $qb
                ->andWhere('(c.lieuArrivee LIKE :to OR c.adresseArrivee LIKE :to)')
                ->setParameter('to', '%' . $filters['to'] . '%');
        }

        // Date de départ (recherche EXACTEMENT ce jour-là pour coller à l'US3)
        if (!empty($filters['date'])) {
            try {
                $date = new \DateTimeImmutable($filters['date']);
                $qb
                    ->andWhere('c.dateDepart = :date')
                    ->setParameter('date', $date->format('Y-m-d'));
            } catch (\Exception $e) {
                // date invalide => on ignore le filtre
            }
        }

        // Trajets "éco" uniquement
        // Un voyage est considéré écologique s’il est effectué avec une voiture électrique (US3)
        if (!empty($filters['eco'])) {
            $qb
                ->andWhere('LOWER(v.energie) LIKE :electric')
                ->setParameter('electric', '%electrique%');
        }

        // Prix max / personne
        if (isset($filters['price_max']) && $filters['price_max'] !== '' && $filters['price_max'] !== null) {
            $qb
                ->andWhere('c.prixPersonne <= :priceMax')
                ->setParameter('priceMax', (float) $filters['price_max']);
        }

        // Note minimale (moyenne des avis du conducteur)
        $hasRatingFilter = isset($filters['rating_min']) && $filters['rating_min'] !== null && $filters['rating_min'] !== '';
        if ($hasRatingFilter) {
            $qb
                // Jointure sur Avis via le conducteur u
                ->leftJoin('App\Entity\Avis', 'a', 'WITH', 'a.utilisateur = u')
                // Moyenne des notes en colonne cachée
                ->addSelect('AVG(a.note) AS HIDDEN avgNote')
                // Group By obligatoires pour utiliser AVG()
                ->groupBy('c.id')
                ->addGroupBy('v.id')
                ->addGroupBy('u.id')
                ->having('avgNote >= :ratingMin')
                ->setParameter('ratingMin', (float) $filters['rating_min']);
        }

        /** @var Covoiturage[] $allRides */
        $allRides = $qb->getQuery()->getResult();

        // --- Filtre durée max (en heures) côté PHP ---
        $durationMaxHours = null;
        if (isset($filters['duration_max']) && $filters['duration_max'] !== '' && $filters['duration_max'] !== null) {
            $durationMaxHours = (int) $filters['duration_max'];
        }

        if ($durationMaxHours !== null) {
            $maxMinutes = $durationMaxHours * 60;

            $allRides = array_filter($allRides, function (Covoiturage $covoiturage) use ($maxMinutes) {
                $start = $covoiturage->getHeureDepart();
                $end   = $covoiturage->getHeureArrivee();

                // Si pas d'heure d'arrivée, on ne filtre pas (comportement discutable, mais safe)
                if (!$start || !$end) {
                    return true;
                }

                $interval = $start->diff($end);
                $minutes  = $interval->h * 60 + $interval->i;

                return $minutes <= $maxMinutes;
            });
        }

        // --- IMPORTANT : ne garder que les trajets avec au moins 1 place disponible (US3) ---
        $allRides = array_filter($allRides, function (Covoiturage $covoiturage) {
            return $covoiturage->getPlacesRestantes() > 0;
        });

        // Réindexer les clés après les filtres PHP
        $allRides = array_values($allRides);

        // --- Pagination en PHP ---
        $total = count($allRides);
        $pages = max(1, (int) ceil($total / $limit));

        if ($page > $pages) {
            $page = $pages;
        }
        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $limit;
        $items  = array_slice($allRides, $offset, $limit);

        return [
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'pages' => $pages,
            'limit' => $limit,
        ];
    }

    /**
     * Version sans pagination, réutilisant la méthode principale.
     */
    public function searchPublicRides(
        ?string $from,
        ?string $to,
        ?\DateTimeInterface $date,
        bool $ecoOnly = false,
        ?float $priceMax = null,
        ?int $durationMaxHours = null,
        ?float $ratingMin = null
    ): array {
        $filters = [
            'from'         => $from,
            'to'           => $to,
            'date'         => $date ? $date->format('Y-m-d') : null,
            'eco'          => $ecoOnly ? 1 : null,
            'price_max'    => $priceMax,
            'duration_max' => $durationMaxHours,
            'rating_min'   => $ratingMin,
        ];

        // On récupère "beaucoup" de lignes, puis la méthode gère tout
        $result = $this->searchWithFiltersAndPagination($filters, 1, 1000);

        return $result['items'];
    }

    /**
     * Récupère les derniers trajets créés (pour la page d'accueil).
     *
     * @param int $limit Nombre de trajets à retourner
     *
     * @return Covoiturage[]
     */
    public function findLastRides(int $limit = 3): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')
            ->addSelect('v')
            ->leftJoin('c.utilisateur', 'u')
            ->addSelect('u')
            ->andWhere('(c.statut IS NULL OR c.statut = :openStatus)')
            ->setParameter('openStatus', 'OUVERT')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Optionnel mais utile pour l’US3 :
     * Trouver la prochaine date disponible (avec au moins 1 place)
     * pour un couple ville départ / ville arrivée, à partir d’une date donnée.
     */
    public function findNextAvailableDateForRoute(
        ?string $from,
        ?string $to,
        \DateTimeInterface $fromDate
    ): ?\DateTimeInterface {
        $qb = $this->createQueryBuilder('c')
            ->select('c.dateDepart')
            ->orderBy('c.dateDepart', 'ASC')
            ->setMaxResults(1)
            ->andWhere('c.dateDepart > :fromDate')
            ->setParameter('fromDate', $fromDate->format('Y-m-d'));

        if (!empty($from)) {
            $qb
                ->andWhere('(c.lieuDepart LIKE :from OR c.adresseDepart LIKE :from)')
                ->setParameter('from', '%' . $from . '%');
        }

        if (!empty($to)) {
            $qb
                ->andWhere('(c.lieuArrivee LIKE :to OR c.adresseArrivee LIKE :to)')
                ->setParameter('to', '%' . $to . '%');
        }

        $result = $qb->getQuery()->getOneOrNullResult();

        if (!$result) {
            return null;
        }

        // Doctrine renvoie déjà un \DateTimeInterface normalement
        if ($result instanceof \DateTimeInterface) {
            return $result;
        }

        // Si renvoyé sous forme de tableau
        if (is_array($result) && isset($result['dateDepart']) && $result['dateDepart'] instanceof \DateTimeInterface) {
            return $result['dateDepart'];
        }

        return null;
    }
}
