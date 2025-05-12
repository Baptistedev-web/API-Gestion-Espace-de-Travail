<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\EspaceTravailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: EspaceTravailRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap([
    "bureau" => Bureau::class,
    "salle_reunion" => SalleReunion::class,
    "espace_collaboration" => EspaceCollaboration::class
])]
#[ApiResource(
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    normalizationContext: [
        'groups' => [
            'getEspacesTravail',
            'getBureaux',
            'getSallesReunion',
            'getEspacesCollaboration'
        ]
    ],
    operations: [
        new Get(
            description: 'Récupère une ressources d\'EspaceTravail.',
            normalizationContext: ['groups' => ['getEspacesTravail']]
        ),
        new GetCollection(
            description: 'Récupère une collection de ressource EspaceTravail.',
            normalizationContext: ['groups' => ['getEspacesTravail']]
        )
    ],
    formats: ['jsonld', 'json'],
    cacheHeaders: [
        'max_age' => 3600, // Cache pour 1 heure
        'shared_max_age' => 3600,
        'vary' => ['Authorization', 'Accept-Language'],
    ],
)]
class EspaceTravail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getEspacesTravail", "getBureaux", "getSallesReunion", "getEspacesCollaboration","getReservationsEspaces"])]
    private int $id = 0;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Veuillez renseigner le nom de l'espace de travail")]
    #[Assert\Length(
        min: 1,
        max: 100,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractère",
        maxMessage: "Le nom ne peux pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s\-\'éèêàç]+$/u',
        message: "Le nom contient des caractères non autorisés"
    )]
    #[Groups(["getEspacesTravail", "getBureaux", "getSallesReunion", "getEspacesCollaboration","getReservationsEspaces"])]
    private string $nom;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Veuillez fournir une description pour l'espace de travail")]
    #[Assert\Length(
        min: 1,
        max: 1000,
        minMessage: "La description doit contenir au moins {{ limit }} caractère",
        maxMessage: "La description ne peux pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s\.,;:!?\-\'"éèêàç]+$/u',
        message: "La description contient des caractères non autorisés"
    )]
    #[Groups(["getEspacesTravail", "getBureaux", "getSallesReunion", "getEspacesCollaboration","getReservationsEspaces"])]
    private string $description;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez indiquer la capacité de l'espace de travail")]
    #[Assert\Positive(message: "La capacité doit être un nombre positif")]
    #[Assert\Range(
        min: 1,
        max: 1000,
        notInRangeMessage: "La capacité doit être comprise entre {{ min }} et {{ max }}"
    )]
    #[Groups(["getEspacesTravail", "getBureaux", "getSallesReunion", "getEspacesCollaboration","getReservationsEspaces"])]
    private int $capacite;

    /**
     * @var Collection<int, ReservationEspace>
     */
    #[ORM\OneToMany(targetEntity: ReservationEspace::class, mappedBy: 'EspaceTravail', orphanRemoval: true)]
    #[Groups(["getEspacesTravail", "getBureaux", "getSallesReunion", "getEspacesCollaboration"])]
    private Collection $reservationEspaces;

    public function __construct(string $nom, string $description, int $capacite)
    {
        $this->nom = $nom;
        $this->description = $description;
        $this->capacite = $capacite;
        $this->reservationEspaces = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCapacite(): int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

        return $this;
    }
    /**
     * @return array<string, string>
     */
    #[Groups(['getEspacesTravail'])]
    public function getLinks(): array
    {
        return [
            'self' => '/api/espaces_travail/' . $this->id,
            'update' => '/api/espaces_travail/' . $this->id,
            'delete' => '/api/espaces_travail/' . $this->id,
        ];
    }

    /**
     * @return Collection<int, ReservationEspace>
     */
    public function getReservationEspaces(): Collection
    {
        return $this->reservationEspaces;
    }

    public function addReservationEspace(ReservationEspace $reservationEspace): static
    {
        if (!$this->reservationEspaces->contains($reservationEspace)) {
            $this->reservationEspaces->add($reservationEspace);
            $reservationEspace->setEspaceTravail($this);
        }

        return $this;
    }

    public function removeReservationEspace(ReservationEspace $reservationEspace): static
    {
        if ($this->reservationEspaces->removeElement($reservationEspace)) {
            // set the owning side to null (unless already changed)
            if ($reservationEspace->getEspaceTravail() === $this) {
                throw new \LogicException("Impossible de supprimer l'espace de travail d'une réservation.");
            }
        }

        return $this;
    }
}

