<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/bureaux',
            description: 'Récupère une collection de ressources Bureau.',
            normalizationContext: ['groups' => ['getBureaux']]
        ),
        new Get(
            uriTemplate: '/bureaux/{id}',
            description: 'Récupère une ressource Bureau.',
            normalizationContext: ['groups' => ['getBureaux']]
        ),
        new Post(
            uriTemplate: '/bureaux',
            description: 'Crée une ressource Bureau.',
            denormalizationContext: ['groups' => ['getBureaux']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            uriTemplate: '/bureaux/{id}',
            description: 'Met à jour une ressource Bureau.',
            denormalizationContext: ['groups' => ['getBureaux']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            uriTemplate: '/bureaux/{id}',
            description: 'Supprime une ressource Bureau.',
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    normalizationContext: ['groups' => ['getBureaux']],
    denormalizationContext: ['groups' => ['getBureaux']],
    formats: ['jsonld', 'json'],
    cacheHeaders: [
        'max_age' => 3600, // Cache pour 1 heure
        'shared_max_age' => 3600,
        'vary' => ['Authorization', 'Accept-Language'],
    ],
)]
class Bureau extends EspaceTravail
{
    #[ORM\Column()]
    #[Assert\NotBlank(message: "Veuillez indiquer le nombre de postes disponibles")]
    #[Assert\Positive(message: "Le nombre de postes doit être un nombre positif")]
    #[Assert\Range(
        min: 1,
        max: 1000,
        notInRangeMessage: "Le nombre de postes doit être compris entre {{ min }} et {{ max }}"
    )]
    #[Groups(["getBureaux", "getEspacesTravail"])]
    private int $nombrePoste = 1;

    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull(message: "Veuillez indiquer si le bureau est disponible en permanent")]
    #[Groups(["getBureaux", "getEspacesTravail"])]
    private bool $disponibleEnPermanent = false;

    #[ORM\ManyToOne(inversedBy: 'Bureau')]
    #[ORM\JoinColumn(nullable: false)]
    private TypeBureau $typeBureau;

    public function __construct(TypeBureau $typeBureau)
    {
        $this->typeBureau = $typeBureau;
    }
    public function getNombrePoste(): int
    {
        return $this->nombrePoste;
    }
    public function setNombrePoste(int $nombrePoste): static
    {
        $this->nombrePoste = $nombrePoste;

        return $this;
    }
    public function getDisponibleEnPermanent(): bool
    {
        return $this->disponibleEnPermanent;
    }
    public function isDisponibleEnPermanent(): bool
    {
        return $this->disponibleEnPermanent;
    }
    public function setDisponibleEnPermanent(bool $disponibleEnPermanent): static
    {
        $this->disponibleEnPermanent = $disponibleEnPermanent;

        return $this;
    }
    /**
     * @return array<string, string>
     */
    #[Groups(['getBureaux'])]
    public function getLinks(): array
    {
        return [
            'self' => '/api/bureaux/' . $this->getId(),
            'update' => '/api/bureaux/' . $this->getId(),
            'delete' => '/api/bureaux/' . $this->getId(),
        ];
    }

    public function getTypeBureau(): TypeBureau
    {
        return $this->typeBureau;
    }

    public function setTypeBureau(TypeBureau $typeBureau): static
    {
        $this->typeBureau = $typeBureau;

        return $this;
    }
}