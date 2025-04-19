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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
#[ApiResource(
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    normalizationContext: ['groups' => ['equipements']],
    denormalizationContext: ['groups' => ['equipements']],
    operations: [
        new GetCollection(
            description: 'Récupérer la liste des équipements',
            normalizationContext: ['groups' => ['equipements']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Get(
            description: 'Récupérer un équipement par son ID',
            normalizationContext: ['groups' => ['equipements']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            description: 'Créer un nouvel équipement',
            denormalizationContext: ['groups' => ['equipements']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            description: 'Mettre à jour un équipement existant',
            denormalizationContext: ['groups' => ['equipements']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            description: 'Supprimer un équipement',
            security: "is_granted('ROLE_ADMIN')"
        ),
    ],
    formats: ['jsonld', 'json'],
    cacheHeaders: [
        'max_age' => 3600, // Cache pour 1 heure
        'shared_max_age' => 3600,
        'vary' => ['Authorization', 'Accept-Language'],
    ]
)]
class Equipement
{
    /**
     * @var int|null Géré automatiquement par Doctrine
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipements'])]
    private ?int $id = null;

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
    #[Groups(['equipements'])]
    private ?string $nom = null;

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
    #[Groups(['equipements'])]
    private ?string $description = null;

    /**
     * @return array<string, string>
     */
    #[Groups(['equipements'])]
    public function getLinks(): array<string, string>
    {
        return [
            'self' => '/api/equipements/' . $this->id,
            'update' => '/api/equipements/' . $this->id,
            'delete' => '/api/equipements/' . $this->id,
        ];
    }
    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
