<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\VoitureRepository;


/**
 * Véhicule déclaré par un utilisateur conducteur.
 * Les trajets référencent cette entité pour afficher marque/modèle/énergie
 * et pour filtrer (voyage écologique, nombre de places, etc.).
 */
#[ORM\Entity]
#[ORM\Table(name: "voiture")]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "voiture_id", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "modele", type: "string", length: 50)]
    private string $modele;

    #[ORM\Column(name: "immatriculation", type: "string", length: 50)]
    private string $immatriculation;

    #[ORM\Column(name: "energie", type: "string", length: 50)]
    private string $energie;

    #[ORM\Column(name: "couleur", type: "string", length: 50)]
    private string $couleur;

    #[ORM\Column(name: "date_premiere_immatriculation", type: "string", length: 50, nullable: true)]
    private ?string $datePremiereImmatriculation = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "utilisateur_id", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Marque::class)]
    #[ORM\JoinColumn(name: "marque_id", referencedColumnName: "marque_id", nullable: false)]
    private ?Marque $marque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModele(): string
    {
        return $this->modele;
    }

    public function setModele(string $modele): self
    {
        $modele = trim($modele);
        $this->modele = $modele;
        return $this;
    }

    public function getImmatriculation(): string
    {
        return $this->immatriculation;
    }

        public function setImmatriculation(string $immatriculation): self
    {
        $this->immatriculation = strtoupper(trim($immatriculation));
        return $this;
    }

    public function getEnergie(): string
    {
        return $this->energie;
    }

    public function setEnergie(string $energie): self
    {
        $this->energie = $energie;
        return $this;
    }

    public function getCouleur(): string
    {
        return $this->couleur;
    }

    public function setCouleur(string $couleur): self
    {
        $this->couleur = $couleur;
        return $this;
    }

    public function getDatePremiereImmatriculation(): ?string
    {
        return $this->datePremiereImmatriculation;
    }

    public function setDatePremiereImmatriculation(?string $date): self
    {
        $this->datePremiereImmatriculation = $date;
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

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): self
    {
        $this->marque = $marque;
        return $this;
    }
}
