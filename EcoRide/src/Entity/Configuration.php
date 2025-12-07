<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Stocke les préférences d’un utilisateur (clé/valeur via Parametre).
 * Une Configuration = 1 utilisateur / n paramètres personnalisables
 * (préférences conducteur, options d’affichage, etc.).
 */
#[ORM\Entity]
#[ORM\Table(name: "configuration")]
class Configuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        name: "id_configuration",
        type: "integer",
        options: ["unsigned" => true]
    )]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "configurations")]
    #[ORM\JoinColumn(
        name: "utilisateur_id",
        referencedColumnName: "utilisateur_id",
        nullable: false
    )]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(
        mappedBy: "configuration",
        targetEntity: Parametre::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $parametres;

    public function __construct()
    {
        $this->parametres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Parametre>
     */
    public function getParametres(): Collection
    {
        return $this->parametres;
    }

    public function addParametre(Parametre $parametre): self
    {
        if (!$this->parametres->contains($parametre)) {
            $this->parametres->add($parametre);
            $parametre->setConfiguration($this);
        }

        return $this;
    }

    public function removeParametre(Parametre $parametre): self
    {
        if ($this->parametres->removeElement($parametre)) {
            // Set the owning side to null (unless already changed)
            if ($parametre->getConfiguration() === $this) {
                $parametre->setConfiguration(null);
            }
        }

        return $this;
    }

    /**
     * Récupère la valeur d'un paramètre par son "propriete".
     *
     * Exemple : getParametreValue('profil_type') => "driver"
     */
    public function getParametreValue(string $propriete): ?string
    {
        foreach ($this->parametres as $parametre) {
            if ($parametre->getPropriete() === $propriete) {
                return $parametre->getValeur();
            }
        }

        return null;
    }

    /**
     * Définit ou met à jour un paramètre (clé/valeur).
     *
     * Exemple :
     *  - setParametreValue('profil_type', 'driver')
     *  - setParametreValue('notif_email', '1')
     */
    public function setParametreValue(string $propriete, ?string $valeur): self
    {
        foreach ($this->parametres as $parametre) {
            if ($parametre->getPropriete() === $propriete) {
                $parametre->setValeur($valeur);

                return $this;
            }
        }

        // Si non trouvé : on crée un nouveau paramètre
        $parametre = new Parametre();
        $parametre
            ->setConfiguration($this)
            ->setPropriete($propriete)
            ->setValeur($valeur);

        $this->parametres->add($parametre);

        return $this;
    }
}
