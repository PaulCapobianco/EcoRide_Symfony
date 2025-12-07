<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rôle applicatif (ROLE_USER, ROLE_EMPLOYE, ROLE_ADMIN…).
 * Stocké en base afin de pouvoir assigner/suspendre facilement un utilisateur.
 */
#[ORM\Entity]
#[ORM\Table(name: "role")]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "role_id", type: "integer")]
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
}
