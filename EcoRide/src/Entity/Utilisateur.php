<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Compte utilisateur EcoRide (passager, conducteur, employé, etc.).
 * Centralise l’authentification, la gestion des crédits, les préférences
 * et l’état des workflows (email vérifié, reset password, rôle principal…).
 */
#[ORM\Entity]
#[ORM\Table(name: "utilisateur")]
#[Vich\Uploadable]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        name: "utilisateur_id",
        type: "integer",
        options: ["unsigned" => true]
    )]
    private ?int $id = null;

    #[ORM\Column(name: "nom", type: "string", length: 50)]
    private string $nom;

    #[ORM\Column(name: "prenom", type: "string", length: 50)]
    private string $prenom;

    #[ORM\Column(name: "email", type: "string", length: 50)]
    private string $email;

    #[ORM\Column(name: "password", type: "string", length: 255)]
    private string $password;

    #[ORM\Column(name: "telephone", type: "string", length: 50, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: "adresse", type: "string", length: 50, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: "date_naissance", type: "string", length: 50, nullable: true)]
    private ?string $dateNaissance = null;

    #[ORM\Column(name: "pseudo", type: "string", length: 50, nullable: true)]
    private ?string $pseudo = null;

    #[ORM\Column(
        name: "credit",
        type: "integer",
        options: ["default" => 0]
    )]
    private int $credit = 0;

    #[ORM\Column(
        name: "profil_type",
        type: "string",
        length: 10,
        options: ["default" => "passenger"]
    )]
    private string $profilType = 'passenger';

    #[ORM\Column(name: "actif", type: "boolean", options: ["default" => true])]
    private bool $active = true;

    #[ORM\Column(
        name: "email_verifie",
        type: "boolean",
        options: ["default" => true]
    )]
    private bool $emailVerified = false;

    #[ORM\Column(
        name: "verification_token",
        type: "string",
        length: 64,
        nullable: true
    )]
    private ?string $verificationToken = null;

    #[ORM\Column(
        name: "verification_requested_at",
        type: "datetime_immutable",
        nullable: true
    )]
    private ?\DateTimeImmutable $verificationRequestedAt = null;

    #[ORM\Column(
        name: "pending_email",
        type: "string",
        length: 50,
        nullable: true
    )]
    private ?string $pendingEmail = null;

    #[ORM\Column(
        name: "email_verified_at",
        type: "datetime_immutable",
        nullable: true
    )]
    private ?\DateTimeImmutable $emailVerifiedAt = null;

    #[ORM\Column(
        name: "reset_password_token",
        type: "string",
        length: 64,
        nullable: true
    )]
    private ?string $resetPasswordToken = null;

    #[ORM\Column(
        name: "reset_requested_at",
        type: "datetime_immutable",
        nullable: true
    )]
    private ?\DateTimeImmutable $resetRequestedAt = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(
        name: "role_id",
        referencedColumnName: "role_id",
        nullable: false
    )]
    private ?Role $role = null;

    /**
     * Configurations/paramètres détenus par l'utilisateur (profil, préférences).
     *
     * @var Collection<int, Configuration>
     */
    #[ORM\OneToMany(
        mappedBy: "utilisateur",
        targetEntity: Configuration::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $configurations;

    /**
     * Participations enregistrées en tant que passager.
     *
     * @var Collection<int, Participation>
     */
    #[ORM\OneToMany(
        mappedBy: "utilisateur",
        targetEntity: Participation::class,
        orphanRemoval: true
    )]
    private Collection $participations;

    #[ORM\Column(name: "photo_profil_nom", type: "string", length: 255, nullable: true)]
    private ?string $photoProfilNom = null;

    #[ORM\Column(name: "photo_profil_update_at", type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $photoProfilUpdateAt = null;

    #[Vich\UploadableField(mapping: "utilisateur_photo_profil", fileNameProperty: "photoProfilNom")]
    private ?File $photoFile = null;

    public function __construct()
    {
        $this->configurations = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Mot de passe hashé (BCrypt / Argon2, etc.)
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDateNaissance(): ?string
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?string $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getCredit(): int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): self
    {
        $this->credit = $credit;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(bool $emailVerified): self
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(?string $verificationToken): self
    {
        $this->verificationToken = $verificationToken;

        return $this;
    }

    public function getVerificationRequestedAt(): ?\DateTimeImmutable
    {
        return $this->verificationRequestedAt;
    }

    public function setVerificationRequestedAt(?\DateTimeImmutable $verificationRequestedAt): self
    {
        $this->verificationRequestedAt = $verificationRequestedAt;

        return $this;
    }

    public function getPendingEmail(): ?string
    {
        return $this->pendingEmail;
    }

    public function setPendingEmail(?string $pendingEmail): self
    {
        $this->pendingEmail = $pendingEmail;

        return $this;
    }

    public function getEmailVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function setEmailVerifiedAt(?\DateTimeImmutable $emailVerifiedAt): self
    {
        $this->emailVerifiedAt = $emailVerifiedAt;

        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): self
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getResetRequestedAt(): ?\DateTimeImmutable
    {
        return $this->resetRequestedAt;
    }

    public function setResetRequestedAt(?\DateTimeImmutable $resetRequestedAt): self
    {
        $this->resetRequestedAt = $resetRequestedAt;

        return $this;
    }

    public function getProfilType(): string
    {
        return $this->profilType;
    }

    public function setProfilType(string $profilType): self
    {
        $allowed = ['passenger', 'driver', 'both'];
        if (!in_array($profilType, $allowed, true)) {
            $profilType = 'passenger';
        }

        $this->profilType = $profilType;

        return $this;
    }

    /**
     * True si l'utilisateur se positionne comme conducteur (driver ou both).
     */
    public function isDriverProfile(): bool
    {
        return in_array($this->profilType, ['driver', 'both'], true);
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function setPhotoFile(?File $photoFile = null): void
    {
        $this->photoFile = $photoFile;

        if ($photoFile !== null) {
            // Force un timestamp pour que Doctrine déclenche les listeners Vich
            $this->photoProfilUpdateAt = new \DateTimeImmutable();
        }
    }

    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    public function getPhotoProfilNom(): ?string
    {
        return $this->photoProfilNom;
    }

    public function setPhotoProfilNom(?string $photoProfilNom): void
    {
        $this->photoProfilNom = $photoProfilNom;
    }

    public function getPhotoProfilUpdateAt(): ?\DateTimeImmutable
    {
        return $this->photoProfilUpdateAt;
    }

    public function setPhotoProfilUpdateAt(?\DateTimeImmutable $photoProfilUpdateAt): void
    {
        $this->photoProfilUpdateAt = $photoProfilUpdateAt;
    }

    /**
     * @return Collection<int, Configuration>
     */
    public function getConfigurations(): Collection
    {
        return $this->configurations;
    }

    public function addConfiguration(Configuration $configuration): self
    {
        if (!$this->configurations->contains($configuration)) {
            $this->configurations->add($configuration);
            $configuration->setUtilisateur($this);
        }

        return $this;
    }

    public function removeConfiguration(Configuration $configuration): self
    {
        if ($this->configurations->removeElement($configuration)) {
            if ($configuration->getUtilisateur() === $this) {
                $configuration->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * Renvoie (par convention) la première configuration de l'utilisateur.
     * Tu peux t'en servir comme "configuration principale du profil".
     */
    public function getMainConfiguration(): ?Configuration
    {
        /** @var Configuration|null $config */
        $config = $this->configurations->first() ?: null;

        return $config;
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
            $participation->setUtilisateur($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            if ($participation->getUtilisateur() === $this) {
                $participation->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * Identifiant visuel de l'utilisateur (utilisé pour le login).
     * Ici, on utilise l'email.
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * Ancienne méthode pour compatibilité (Symfony < 5.3)
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getRoles(): array
    {
        $roles = [];

        if ($this->role instanceof Role) {
            $roles[] = $this->role->getLibelle();
        }

        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Permet d'effacer d'éventuelles données sensibles temporaires (plainPassword, etc.).
     */
    public function eraseCredentials(): void
    {
    }

    public function __serialize(): array
    {
        return [
            'id'             => $this->id,
            'email'          => $this->email,
            'password'       => $this->password,
            'email_verified' => $this->emailVerified,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id            = $data['id'] ?? null;
        $this->email         = $data['email'] ?? '';
        $this->password      = $data['password'] ?? '';
        $this->emailVerified = (bool) ($data['email_verified'] ?? false);
    }
}
