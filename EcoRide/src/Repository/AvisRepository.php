<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Requêtes custom sur les avis (moyennes, pagination chauffeur…)
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    /**
     * Retourne un tableau associatif des notes moyennes par utilisateur :
     *
     * [
     *   3 => ['avg' => 4.5, 'count' => 2],
     *   4 => ['avg' => 3.8, 'count' => 1],
     *   ...
     * ]
     *
     * @param int[] $userIds Liste optionnelle d'identifiants d'utilisateurs
     *                       (pour ne calculer que ceux présents sur la page).
     */
    public function getAverageRatingsByUser(array $userIds = []): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('IDENTITY(a.utilisateur) AS userId, AVG(a.note) AS avgNote, COUNT(a.id) AS avisCount')
            ->where('a.note IS NOT NULL')
            ->andWhere('a.statut = :statut')
            ->setParameter('statut', 'VALIDE');

        if (!empty($userIds)) {
            $qb->andWhere('a.utilisateur IN (:ids)')
               ->setParameter('ids', $userIds);
        }

        $qb->groupBy('a.utilisateur');

        $rows = $qb->getQuery()->getArrayResult();

        $result = [];
        foreach ($rows as $row) {
            if (empty($row['userId'])) {
                continue;
            }

            $userId = (int) $row['userId'];

            $result[$userId] = [
                'avg'   => isset($row['avgNote'])   ? (float) $row['avgNote']   : 0.0,
                'count' => isset($row['avisCount']) ? (int) $row['avisCount'] : 0,
            ];
        }

        return $result;
    }

    /**
     * Moyenne des notes reçues par un conducteur (via ses trajets).
     *
     * @return array{avg: float, count: int}
     */
    public function getAverageForDriver(int $driverId): array
    {
        $row = $this->createQueryBuilder('a')
            ->select('AVG(a.note) AS avgNote, COUNT(a.id) AS avisCount')
            ->innerJoin('a.covoiturage', 'c')
            ->andWhere('c.utilisateur = :driver')
            ->andWhere('a.note IS NOT NULL')
            ->andWhere('a.statut = :statut')
            ->setParameter('driver', $driverId)
            ->setParameter('statut', 'VALIDE')
            ->getQuery()
            ->getSingleResult();

        return [
            'avg'   => isset($row['avgNote']) ? (float) $row['avgNote'] : 0.0,
            'count' => isset($row['avisCount']) ? (int) $row['avisCount'] : 0,
        ];
    }

    /**
     * Derniers avis validés reçus par un conducteur (via ses covoiturages).
     *
     * @return Avis[]
     */
    public function findLastForDriver(int $driverId, int $limit = 10): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.covoiturage', 'c')
            ->addSelect('c')
            ->leftJoin('a.utilisateur', 'u')
            ->addSelect('u')
            ->andWhere('c.utilisateur = :driver')
            ->andWhere('a.statut = :statut')
            ->setParameter('driver', $driverId)
            ->setParameter('statut', 'VALIDE')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countForDriver(int $driverId): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->innerJoin('a.covoiturage', 'c')
            ->andWhere('c.utilisateur = :driver')
            ->andWhere('a.statut = :statut')
            ->setParameter('driver', $driverId)
            ->setParameter('statut', 'VALIDE')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Avis[]
     */
    public function findForDriverPaginated(int $driverId, int $limit, int $offset): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.covoiturage', 'c')
            ->addSelect('c')
            ->leftJoin('a.utilisateur', 'u')
            ->addSelect('u')
            ->andWhere('c.utilisateur = :driver')
            ->andWhere('a.statut = :statut')
            ->setParameter('driver', $driverId)
            ->setParameter('statut', 'VALIDE')
            ->orderBy('a.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
