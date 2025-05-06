<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\PUT;
use ApiPlatform\Metadata\Link;
use App\State\UserStateProcessor;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['getUsers']],
    denormalizationContext: ['groups' => ['getUsers']],
    operations: [
        new Post(
            description: "Crée une ressource Utilisateur.",
            denormalizationContext: ['groups' => ['getUsers']],
            security: "is_granted('PUBLIC_ACCESS')",
            processor: UserStateProcessor::class,
            validationContext: ['groups' => ['getUsers']]
        ),
        new Delete(
            description: "Supprime la ressource Utilisateur.",
            security: "is_granted('ROLE_USER') and object == user"
        ),
        new Get(
            description: "Récupère une ressource Utilisateur.",
            normalizationContext: ['groups' => ['getUsers']],
            security: "is_granted('ROLE_USER') and object == user"
        ),
        new PUT(
            description: "Remplace la ressource Utilisateur.",
            denormalizationContext: ['groups' => ['getUsers']],
            normalizationContext: ['groups' => ['getUsers']],
            security: "is_granted('ROLE_USER') and object == user",
            processor: UserStateProcessor::class,
            validationContext: ['groups' => ['getUsers']]
        ),
    ],
    formats: ['jsonld', 'json'],
    cacheHeaders: [
        'max_age' => 3600, // Cache pour 1 heure
        'shared_max_age' => 3600,
        'vary' => ['Authorization', 'Accept-Language'],
    ],
)]
#[UniqueEntity(
    fields: ['email'],
    message: "L'email {{ value }} est déjà utilisé par un autre utilisateur."
)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int|null Géré automatiquement par Doctrine
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getUsers", "getReservations"])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email ne doit pas être vide.")]
    #[Assert\Email(
        mode: 'strict',
        message: "L'adresse e-mail {{ value }} n'est pas valide."
    )]
    #[Groups(["getUsers", "getReservations"])]
    private ?string $email = null;
    
    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(["getUsers"])]
    private array $roles = [];
    
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le mot de passe ne doit pas être vide.")]
    #[Assert\Length(
        min: 12,
        max: 255,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le mot de passe ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/',
        message: "Le mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial."
    )]
    #[Groups(["getUsers"])]
    private ?string $password = null;
    
    /**
     * @var string|null Plain password for validation and hash
     */
    #[SerializedName('password')]
    #[Groups(["getUsers"])]
    private ?string $plainPassword = null;
    
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom ne doit pas être vide.")]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "Le nom de famille doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de famille ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ\s\-]+$/',
        message: "Le nom de famille ne doit contenir que des lettres avec ou sans accents, des espaces ou des tirets."
    )]
    #[Groups(["getUsers", "getReservations"])]
    private ?string $nom = null;
    
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le prénom ne doit pas être vide.")]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le prénom ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ\s\-]+$/',
        message: "Le prénom ne doit contenir que des lettres avec ou sans accents, des espaces ou des tirets."
    )]
    #[Groups(["getUsers", "getReservations"])]
    private ?string $prenom = null;

    /**
     * @var Collection<int, ReservationEquipement>
     */
    #[ORM\OneToMany(targetEntity: ReservationEquipement::class, mappedBy: 'User', orphanRemoval: true)]
    #[Groups(["getUsers"])]
    private Collection $reservationEquipements;

    public function __construct()
    {
        $this->reservationEquipements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }
    /**
     * Méthode getUsername qui permet de retourner le champ qui est utilisé pour l'authentification
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        $this->plainPassword = null; // Clear plain password after hash

        return $this;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    #[Groups(['getUsers'])]
    public function getLinks(): array
    {
        return [
            'self' => '/api/users/' . $this->id,
            'update' => '/api/users/' . $this->id,
            'delete' => '/api/users/' . $this->id,
        ];
    }

    /**
     * @return Collection<int, ReservationEquipement>
     */
    public function getReservationEquipements(): Collection
    {
        return $this->reservationEquipements;
    }

    public function addReservationEquipement(ReservationEquipement $reservationEquipement): static
    {
        if (!$this->reservationEquipements->contains($reservationEquipement)) {
            $this->reservationEquipements->add($reservationEquipement);
            $reservationEquipement->setUser($this);
        }

        return $this;
    }

    public function removeReservationEquipement(ReservationEquipement $reservationEquipement): static
    {
        if ($this->reservationEquipements->removeElement($reservationEquipement)) {
            // set the owning side to null (unless already changed)
            if ($reservationEquipement->getUser() === $this) {
                $reservationEquipement->setUser(null);
            }
        }

        return $this;
    }
}
