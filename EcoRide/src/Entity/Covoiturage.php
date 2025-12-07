<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Trajet planifié sur EcoRide : infos de parcours + statut métier.
 * Porte les relations vers le conducteur, le véhicule, les participations
 * passagers et les avis reçus après coup.
 */
#[ORM\Entity]
#[ORM\Table(name: "covoiturage")]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "covoiturage_id", type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: "lieu_depart", type: Types::STRING, length: 100)]
    private string $lieuDepart;

    #[ORM\Column(name: "lieu_arrivee", type: Types::STRING, length: 100)]
    private string $lieuArrivee;

    #[ORM\Column(name: "adresse_depart", type: Types::STRING, length: 255, nullable: true)]
    private ?string $adresseDepart = null;

    #[ORM\Column(name: "adresse_arrivee", type: Types::STRING, length: 255, nullable: true)]
    private ?string $adresseArrivee = null;

    #[ORM\Column(name: "date_depart", type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $dateDepart;

    #[ORM\Column(name: "date_arrivee", type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $dateArrivee;

    #[ORM\Column(name: "heure_depart", type: Types::TIME_MUTABLE)]
    private \DateTimeInterface $heureDepart;

    #[ORM\Column(name: "heure_arrivee", type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureArrivee = null;

    #[ORM\Column(name: "nb_place", type: Types::INTEGER)]
    private int $nbPlace;

    #[ORM\Column(name: "prix_personne", type: Types::INTEGER)]
    private int $prixPersonne;

    #[ORM\Column(name: "statut", type: Types::STRING, length: 20)]
    private string $statut = 'OUVERT';

    #[ORM\Column(name: "started_at", type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(name: "finished_at", type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $finishedAt = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(
        name: "utilisateur_id",
        referencedColumnName: "utilisateur_id",
        nullable: false
    )]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Voiture::class)]
    #[ORM\JoinColumn(
        name: "voiture_id",
        referencedColumnName: "voiture_id",
        nullable: true
    )]
    private ?Voiture $voiture = null;

    /**
     * Inscription des passagers (entité pivot Participation).
     *
     * @var Collection<int, Participation>
     */
    #[ORM\OneToMany(
        mappedBy: "covoiturage",
        targetEntity: Participation::class,
        orphanRemoval: true
    )]
    private Collection $participations;

    /**
     * Avis laissés sur ce trajet (permet d'afficher l'historique côté chauffeur).
     *
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(
        mappedBy: "covoiturage",
        targetEntity: Avis::class,
        orphanRemoval: true
    )]
    private Collection $avisList;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->avisList       = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLieuDepart(): string
    {
        return $this->lieuDepart;
    }

    public function setLieuDepart(string $lieuDepart): self
    {
        $this->lieuDepart = $lieuDepart;

        return $this;
    }

    public function getLieuArrivee(): string
    {
        return $this->lieuArrivee;
    }

    public function setLieuArrivee(string $lieuArrivee): self
    {
        $this->lieuArrivee = $lieuArrivee;

        return $this;
    }

    public function getAdresseDepart(): ?string
    {
        return $this->adresseDepart;
    }

    public function setAdresseDepart(?string $adresseDepart): self
    {
        $this->adresseDepart = $adresseDepart;

        return $this;
    }

    public function getAdresseArrivee(): ?string
    {
        return $this->adresseArrivee;
    }

    public function setAdresseArrivee(?string $adresseArrivee): self
    {
        $this->adresseArrivee = $adresseArrivee;

        return $this;
    }

    public function getDateDepart(): \DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTimeInterface $dateDepart): self
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getDateArrivee(): \DateTimeInterface
    {
        return $this->dateArrivee;
    }

    public function setDateArrivee(\DateTimeInterface $dateArrivee): self
    {
        $this->dateArrivee = $dateArrivee;

        return $this;
    }

    public function getHeureDepart(): \DateTimeInterface
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(\DateTimeInterface $heureDepart): self
    {
        $this->heureDepart = $heureDepart;

        return $this;
    }

    public function getHeureArrivee(): ?\DateTimeInterface
    {
        return $this->heureArrivee;
    }

    public function setHeureArrivee(?\DateTimeInterface $heureArrivee): self
    {
        $this->heureArrivee = $heureArrivee;

        return $this;
    }

    public function getNbPlace(): int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): self
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getPrixPersonne(): int
    {
        return $this->prixPersonne;
    }

    public function setPrixPersonne(int $prixPersonne): self
    {
        $this->prixPersonne = $prixPersonne;

        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeImmutable $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): self
    {
        $this->voiture = $voiture;

        return $this;
    }

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setCovoiturage($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            if ($participation->getCovoiturage() === $this) {
                $participation->setCovoiturage(null);
            }
        }

        return $this;
    }

    /**
     * Nombre de places restantes : correspond directement au champ nbPlace (décrémenté lors des participations).
     */
    public function getPlacesRestantes(): int
    {
        return max(0, (int) $this->nbPlace);
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisList(): Collection
    {
        return $this->avisList;
    }

    public function addAvis(Avis $avis): self
    {
        if (!$this->avisList->contains($avis)) {
            $this->avisList->add($avis);
            $avis->setCovoiturage($this);
        }

        return $this;
    }

    public function removeAvis(Avis $avis): self
    {
        if ($this->avisList->removeElement($avis)) {
            if ($avis->getCovoiturage() === $this) {
                $avis->setCovoiturage(null);
            }
        }

        return $this;
    }
}
