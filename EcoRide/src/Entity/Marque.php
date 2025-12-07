<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Marque de véhicule (référentiel simple) associée aux fiches voiture.
 * Permet de centraliser la liste utilisée dans les formulaires conducteur.
 */
#[ORM\Entity]
#[ORM\Table(name: "marque")]
class Marque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "marque_id", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "libelle", type: "string", length: 50)]
    private string $libelle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }
    public function __toString(): string
    {
        if (\property_exists($this, 'libelle') && $this->libelle !== null) {
            return (string) $this->libelle;
        }

        if (\property_exists($this, 'nom') && $this->nom !== null) {
            return (string) $this->nom;
        }

        return 'Marque #' . (string) $this->id;
    }

}
