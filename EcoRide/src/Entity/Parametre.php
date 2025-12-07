<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Couple clé/valeur lié à une Configuration (ex : music=1, custom="Pas d'enfants").
 */
#[ORM\Entity]
#[ORM\Table(name: "parametre")]
class Parametre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "parametre_id", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "propriete", type: "string", length: 50)]
    private string $propriete;

    #[ORM\Column(name: "valeur", type: "string", length: 50, nullable: true)]
    private ?string $valeur = null;

    #[ORM\ManyToOne(targetEntity: Configuration::class, inversedBy: "parametres")]
    #[ORM\JoinColumn(
        name: "id_configuration",
        referencedColumnName: "id_configuration",
        nullable: false
    )]
    private ?Configuration $configuration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPropriete(): string
    {
        return $this->propriete;
    }

    public function setPropriete(string $propriete): self
    {
        $this->propriete = $propriete;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(?string $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    public function setConfiguration(?Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s=%s', $this->propriete, (string) $this->valeur);
    }
}
