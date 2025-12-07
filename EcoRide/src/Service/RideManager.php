<?php

namespace App\Service;

use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use App\Repository\VoitureRepository;

/**
 * Regroupe la logique métier commune aux actions conducteur :
 * - récupération des véhicules du driver
 * - hydratation d'un trajet depuis le formulaire de publication
 * - remboursement des passagers (sans flush, laissé à l'appelant)
 */
class RideManager
{
    public function __construct(private VoitureRepository $voitureRepository)
    {
    }

    /**
     * @return Voiture[]
     */
    public function getDriverVehicles(Utilisateur $driver): array
    {
        return $this->voitureRepository->findBy([
            'utilisateur' => $driver,
        ]);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function hydrateRideFromForm(Covoiturage $ride, array $data, Utilisateur $driver): void
    {
        $from        = trim((string) ($data['from'] ?? ''));
        $fromAddress = trim((string) ($data['fromAddress'] ?? ''));
        $to          = trim((string) ($data['to'] ?? ''));
        $toAddress   = trim((string) ($data['toAddress'] ?? ''));

        /** @var \DateTimeInterface|null $date */
        $date      = $data['date'] ?? null;
        /** @var \DateTimeInterface|null $timeStart */
        $timeStart = $data['timeStart'] ?? null;
        /** @var \DateTimeInterface|null $timeEnd */
        $timeEnd   = $data['timeEnd'] ?? null;

        /** @var Voiture|null $voiture */
        $voiture = $data['vehicle'] ?? null;

        $seats = (int) ($data['seats'] ?? 0);
        $price = (int) ($data['price'] ?? 0); // crédits

        $ride
            ->setLieuDepart($from)
            ->setAdresseDepart($fromAddress !== '' ? $fromAddress : null)
            ->setLieuArrivee($to)
            ->setAdresseArrivee($toAddress !== '' ? $toAddress : null)
            ->setNbPlace($seats)
            ->setPrixPersonne($price)
            ->setUtilisateur($driver);

        if ($date !== null) {
            $ride->setDateDepart($date);
            $ride->setDateArrivee($date);
        }
        if ($timeStart !== null) {
            $ride->setHeureDepart($timeStart);
        }
        if ($timeEnd !== null) {
            $ride->setHeureArrivee($timeEnd);
        } else {
            $ride->setHeureArrivee(null);
        }

        $ride->setVoiture($voiture instanceof Voiture ? $voiture : null);

        // Statut recalculé en fonction des places
        $ride->setStatut($seats > 0 ? 'OUVERT' : 'COMPLET');
    }

    public function refundPassengers(Covoiturage $covoiturage): void
    {
        $prixParPersonne = (int) $covoiturage->getPrixPersonne();

        foreach ($covoiturage->getParticipations() as $participation) {
            $passenger = $participation->getUtilisateur();
            if (!$passenger instanceof Utilisateur) {
                continue;
            }

            $nbPlaces = (int) ($participation->getNbPlaces() ?? 1);
            $refund   = $prixParPersonne * $nbPlaces;

            $passenger->setCredit($passenger->getCredit() + $refund);
        }
    }
}
