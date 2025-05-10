<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\EquipementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
#[ApiResource(
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    normalizationContext: ['groups' => ['getEquipements']],
    denormalizationContext: ['groups' => ['getEquipements']],
    operations: [
        new GetCollection(
            description: 'Récupère une collection de ressources Équipement.',
            normalizationContext: ['groups' => ['getEquipements']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Get(
            description: 'Récupère une ressource Équipement.',
            normalizationContext: ['groups' => ['getEquipements']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            description: 'Crée une ressource Équipement.',
            denormalizationContext: ['groups' => ['getEquipements']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            description: 'Remplace la ressource Équipement.',
            denormalizationContext: ['groups' => ['getEquipements']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            description: 'Supprime la ressource Équipement.',
            security: "is_granted('ROLE_ADMIN')"
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
    fields: ['nom'],
    message: 'Ce nom est déjà utilisé. Veuillez en choisir un autre.'
)]
class Equipement
{
    /**
     * @var int Géré automatiquement par Doctrine
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getEquipements", "getReservations"])]
    private int $id = 0;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom ne doit pas être vide.')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne doit pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s]+$/u',
        message: 'Le nom ne doit contenir que des lettres (y compris avec accents), des chiffres et des espaces.'
    )]
    #[Groups(["getEquipements", "getReservations"])]
    private string $nom = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description ne doit pas être vide.')]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'La description ne doit pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s]+$/u',
        message: 'La description ne doit contenir que des lettres (y compris avec accents), des chiffres et des espaces.'
    )]
    #[Groups(["getEquipements", "getReservations"])]
    private string $description = '';

    /**
     * @var Collection<int, ReservationEquipement>
     */
    #[ORM\OneToMany(targetEntity: ReservationEquipement::class, mappedBy: 'Equipement', orphanRemoval: true)]
    #[Groups(["getEquipements"])]
    private Collection $reservationEquipements;

    public function __construct()
    {
        $this->reservationEquipements = new ArrayCollection();
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

    /**
     * @return array<string, string>
     */
    #[Groups(['equipements:read'])]
    public function getLinks(): array
    {
        return [
            'self' => '/api/equipements/' . $this->id,
            'update' => '/api/equipements/' . $this->id,
            'delete' => '/api/equipements/' . $this->id,
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
            $reservationEquipement->setEquipement($this);
        }

        return $this;
    }

    public function removeReservationEquipement(ReservationEquipement $reservationEquipement): static
    {
        if ($this->reservationEquipements->removeElement($reservationEquipement)) {
            // Ne pas tenter de définir l'équipement à null car ReservationEquipement attend une instance d'Equipement
            // Ce code est commenté car il provoquerait une erreur de type
            // if ($reservationEquipement->getEquipement() === $this) {
            //     $reservationEquipement->setEquipement(null);
            // }
        }

        return $this;
    }
}
