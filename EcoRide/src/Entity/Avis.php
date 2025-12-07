<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Avis laissé par un passager sur un trajet.
 * Sert de trace qualitative (commentaire/note) mais aussi de workflow :
 *   - statut A_VALIDER / VALIDE / REFUSE pour l’équipe employé
 *   - déclenchement du crédit chauffeur quand la participation est confirmée
 */
#[ORM\Entity(repositoryClass: AvisRepository::class)]
#[ORM\Table(name: "avis")]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "avis_id", type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: "commentaire", type: Types::STRING, length: 50, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(name: "note", type: Types::FLOAT, nullable: true)]
    private ?float $note = null;

    #[ORM\Column(name: "statut", type: Types::STRING, length: 50, nullable: true)]
    private ?string $statut = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(
        name: "utilisateur_id",
        referencedColumnName: "utilisateur_id",
        nullable: true
    )]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Covoiturage::class, inversedBy: "avisList")]
    #[ORM\JoinColumn(
        name: "covoiturage_id",
        referencedColumnName: "covoiturage_id",
        nullable: false
    )]
    private ?Covoiturage $covoiturage = null;

    // ---------- GETTERS / SETTERS ----------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;
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

    public function getCovoiturage(): ?Covoiturage
    {
        return $this->covoiturage;
    }

    public function setCovoiturage(?Covoiturage $covoiturage): self
    {
        $this->covoiturage = $covoiturage;
        return $this;
    }
}
