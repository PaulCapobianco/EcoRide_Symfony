<?php

namespace App\Service;

use App\Entity\Avis;
use App\Entity\Covoiturage;
use App\Entity\Participation;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service métier pour la gestion des participations (réservation, annulation, avis).
 */
class ParticipationManager
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @return array{reason:?string, canParticipate:bool}
     */
    public function canParticipate(Covoiturage $covoiturage, Utilisateur $user): array
    {
        $driver          = $covoiturage->getUtilisateur();
        $placesRestantes = $covoiturage->getNbPlace();
        $priceCredits    = (int) $covoiturage->getPrixPersonne();
        $userCredit      = $user->getCredit();
        $profilType      = $user->getProfilType();
        $statut          = $covoiturage->getStatut();

        if ($placesRestantes <= 0) {
            return ['reason' => 'Ce trajet est déjà complet.', 'canParticipate' => false];
        }

        if (!in_array($statut, ['OUVERT', 'COMPLET'], true)) {
            return ['reason' => 'Ce trajet n’est plus ouvert aux réservations.', 'canParticipate' => false];
        }

        if ($driver && $driver->getId() === $user->getId()) {
            return ['reason' => 'Vous ne pouvez pas participer à votre propre trajet.', 'canParticipate' => false];
        }

        if ($profilType === 'driver') {
            return [
                'reason' => 'Votre profil est configuré comme conducteur uniquement. Activez le mode passager ou "les deux" dans votre profil pour participer à un trajet.',
                'canParticipate' => false,
            ];
        }

        if ($userCredit < $priceCredits) {
            return ['reason' => 'Vous n’avez pas assez de crédits pour participer.', 'canParticipate' => false];
        }

        return ['reason' => null, 'canParticipate' => true];
    }

    /**
     * Enregistre une participation (déduit crédits, décrémente places, met à jour statut) sans flush.
     */
    public function registerParticipation(Covoiturage $covoiturage, Utilisateur $user, int $priceCredits): Participation
    {
        $user->setCredit($user->getCredit() - $priceCredits);
        $newPlaces = max(0, ($covoiturage->getNbPlace() ?? 0) - 1);
        $covoiturage->setNbPlace($newPlaces);

        if ($newPlaces <= 0) {
            $covoiturage->setStatut('COMPLET');
        }

        $participation = new Participation();
        $participation
            ->setCovoiturage($covoiturage)
            ->setUtilisateur($user)
            ->setNbPlaces(1);

        $covoiturage->addParticipation($participation);

        $this->em->persist($participation);

        return $participation;
    }

    /**
     * Annule une participation (remet places, recrédite) sans flush.
     */
    public function cancelParticipation(Participation $participation): int
    {
        $covoiturage = $participation->getCovoiturage();
        if (!$covoiturage instanceof Covoiturage) {
            throw new \InvalidArgumentException('Trajet introuvable pour cette participation.');
        }

        $user = $participation->getUtilisateur();
        if (!$user instanceof Utilisateur) {
            throw new \InvalidArgumentException('Utilisateur introuvable pour cette participation.');
        }

        $nbPlacesParticipation = (int) ($participation->getNbPlaces() ?? 1);
        $prixParPersonne       = (int) $covoiturage->getPrixPersonne();
        $refund                = $prixParPersonne * $nbPlacesParticipation;

        $user->setCredit($user->getCredit() + $refund);
        $covoiturage->setNbPlace(($covoiturage->getNbPlace() ?? 0) + $nbPlacesParticipation);

        if ($covoiturage->getNbPlace() > 0 && $covoiturage->getStatut() === 'COMPLET') {
            $covoiturage->setStatut('OUVERT');
        }

        $this->em->remove($participation);

        return $refund;
    }

    /**
     * Crée ou met à jour un avis pour une participation terminée.
     */
    public function upsertAvisForParticipation(
        Participation $participation,
        ?string $comment,
        ?int $note,
        string $status = 'A_VALIDER'
    ): Avis {
        $covoiturage = $participation->getCovoiturage();
        $user        = $participation->getUtilisateur();

        if (!$covoiturage instanceof Covoiturage || !$user instanceof Utilisateur) {
            throw new \InvalidArgumentException('Participation invalide pour créer un avis.');
        }

        $avisRepo = $this->em->getRepository(Avis::class);
        $avis     = $avisRepo->findOneBy([
            'covoiturage' => $covoiturage,
            'utilisateur' => $user,
        ]) ?? new Avis();

        $avis
            ->setUtilisateur($user)
            ->setCovoiturage($covoiturage)
            ->setCommentaire($comment)
            ->setNote($note !== null ? (float) $note : null)
            ->setStatut($status);

        $this->em->persist($avis);

        return $avis;
    }
}
