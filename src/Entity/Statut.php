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
use App\Repository\StatutRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: StatutRepository::class)]
#[ApiResource(
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    normalizationContext: ['groups' => ['statuts']],
    denormalizationContext: ['groups' => ['statuts']],
    operations: [
        new GetCollection(
            description: 'Récupérer la liste des statuts',
            normalizationContext: ['groups' => ['statuts']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Get(
            description: 'Récupérer un statut par son ID',
            normalizationContext: ['groups' => ['statuts']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            description: 'Créer un nouveau statut',
            denormalizationContext: ['groups' => ['statuts']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            description: 'Mettre à jour un statut existant',
            denormalizationContext: ['groups' => ['statuts']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            description: 'Supprimer un statut',
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
#[UniqueEntity(
    fields: ['libelle'],
    message: 'Ce libellé est déjà utilisé. Veuillez en choisir un autre.'
)]
class Statut
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['statuts'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le libellé ne peut pas être vide')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le libellé doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le libellé ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s]+$/u',
        message: 'Le libellé ne peut contenir que des lettres (y compris avec accents) et des espaces'
    )]
    #[Groups(['statuts'])]
    private ?string $libelle = null;

    /**
     * @return array<string, string>
     */
    #[Groups(['statuts'])]
    public function getLinks(): array<string, string>
    {
        return [
            'self' => '/api/statuts/' . $this->id,
            'update' => '/api/statuts/' . $this->id,
            'delete' => '/api/statuts/' . $this->id,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }
}
