<?php

namespace App\Service;

use App\Entity\Configuration;
use App\Entity\Parametre;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Fournit des helpers réutilisables autour du profil utilisateur (prefs, véhicules, etc.).
 */
class ProfileHelper
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * Construit la structure de préférences attendue par les vues
     * à partir de Configuration + Parametre.
     *
     * @return array{
     *   builtins: array<string,bool>,
     *   custom: list<array{id:int|null,label:string}>
     * }
     */
    public function buildPreferencesForUser(Utilisateur $user): array
    {
        $prefs = [
            'builtins' => [
                'music'     => false,
                'ac'        => false,
                'pets'      => false,
                'baggage'   => false,
                'noSmoking' => false,
            ],
            'custom' => [],
        ];

        $configRepo = $this->em->getRepository(Configuration::class);
        $paramRepo  = $this->em->getRepository(Parametre::class);

        /** @var Configuration|null $config */
        $config = $configRepo->findOneBy(['utilisateur' => $user]);
        if (!$config) {
            return $prefs;
        }

        $params = $paramRepo->findBy(
            ['configuration' => $config],
            ['id' => 'ASC']
        );

        foreach ($params as $param) {
            $prop  = $param->getPropriete();
            $value = $param->getValeur();

            switch ($prop) {
                case 'music':
                    $prefs['builtins']['music'] = true;
                    break;
                case 'ac':
                    $prefs['builtins']['ac'] = true;
                    break;
                case 'pets':
                    $prefs['builtins']['pets'] = true;
                    break;
                case 'baggage':
                    $prefs['builtins']['baggage'] = true;
                    break;
                case 'noSmoking':
                    $prefs['builtins']['noSmoking'] = true;
                    break;
                case 'custom':
                    if ($value !== null && $value !== '') {
                        $prefs['custom'][] = [
                            'id'    => $param->getId(),
                            'label' => $value,
                        ];
                    }
                    break;
            }
        }

        return $prefs;
    }

    /**
     * Retourne true si l'utilisateur possède au moins un véhicule.
     */
    public function userHasAtLeastOneVehicle(Utilisateur $user): bool
    {
        $count = (int) $this->em->getRepository(Voiture::class)
            ->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.utilisateur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
