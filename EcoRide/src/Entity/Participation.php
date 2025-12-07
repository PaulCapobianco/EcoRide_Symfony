<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/**
 * Inscription d’un passager sur un covoiturage.
 * Fait office d’entité pivot pour suivre le statut (PENDING, VALIDATED,
 * REPORTED…) et calculer les crédits à restituer en cas d’annulation.
 */
#[ORM\Entity]
#[ORM\Table(name: "participation")]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id", type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Covoiturage::class, inversedBy: "participations")]
    #[ORM\JoinColumn(
        name: "covoiturage_id",
        referencedColumnName: "covoiturage_id",
        nullable: false,
        onDelete: "CASCADE"
    )]
    private ?Covoiturage $covoiturage = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "participations")]
    #[ORM\JoinColumn(
        name: "utilisateur_id",
        referencedColumnName: "utilisateur_id",
        nullable: false,
        onDelete: "CASCADE"
    )]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $nbPlaces = 1;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: "confirmation_status", type: Types::STRING, length: 20, options: ["default" => "PENDING"])]
    private string $confirmationStatus = 'PENDING';

    #[ORM\Column(name: "confirmation_comment", type: Types::STRING, length: 255, nullable: true)]
    private ?string $confirmationComment = null;

    #[ORM\Column(name: "confirmation_at", type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $confirmationAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->nbPlaces  = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getNbPlaces(): int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): self
    {
        $this->nbPlaces = $nbPlaces;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getConfirmationStatus(): string
    {
        return $this->confirmationStatus;
    }

    public function setConfirmationStatus(string $confirmationStatus): self
    {
        $this->confirmationStatus = $confirmationStatus;
        return $this;
    }

    public function getConfirmationComment(): ?string
    {
        return $this->confirmationComment;
    }

    public function setConfirmationComment(?string $confirmationComment): self
    {
        $this->confirmationComment = $confirmationComment;
        return $this;
    }

    public function getConfirmationAt(): ?\DateTimeImmutable
    {
        return $this->confirmationAt;
    }

    public function setConfirmationAt(?\DateTimeImmutable $confirmationAt): self
    {
        $this->confirmationAt = $confirmationAt;
        return $this;
    }
}
