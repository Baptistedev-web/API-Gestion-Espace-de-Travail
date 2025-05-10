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
            description: 'Récupère une collection de ressources EspaceCollaboration.',
            normalizationContext: ['groups' => ['getEspacesCollaboration']]
        ),
        new Get(
            description: 'Récupère une ressource EspaceCollaboration.',
            normalizationContext: ['groups' => ['getEspacesCollaboration']]
        ),
        new Post(
            description: 'Crée une ressource EspaceCollaboration.',
            denormalizationContext: ['groups' => ['writeEspacesCollaboration']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            description: 'Met à jour une ressource EspaceCollaboration.',
            denormalizationContext: ['groups' => ['writeEspacesCollaboration']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            description: 'Supprime une ressource EspaceCollaboration.',
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    normalizationContext: ['groups' => ['getEspacesCollaboration']],
    denormalizationContext: ['groups' => ['getEspacesCollaboration']],
    formats: ['jsonld', 'json'],
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
)]
class EspaceCollaboration extends EspaceTravail
{
    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull(message: "Veuillez indiquer si le mobilier est modulable.")]
    #[Groups(["getEspacesCollaboration", "getEspacesTravail"])]
    private bool $mobilierModulable = false;

    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull(message: "Veuillez indiquer si une zone café est proche.")]
    #[Groups(["getEspacesCollaboration", "getEspacesTravail"])]
    private bool $zoneCafeProche = false;

    #[ORM\ManyToOne(inversedBy: 'EspaceCollaboration')]
    #[ORM\JoinColumn(nullable: false)]
    private TypeAmbiance $typeAmbiance;

    public function __construct(TypeAmbiance $typeAmbiance)
    {
        $this->typeAmbiance = $typeAmbiance;
    }
    public function getMobilierModulable(): bool
    {
        return $this->mobilierModulable;
    }
    public function isMobilierModulable(): bool
    {
        return $this->mobilierModulable;
    }
    public function setMobilierModulable(bool $mobilierModulable): static
    {
        $this->mobilierModulable = $mobilierModulable;

        return $this;
    }
    public function getZoneCafeProche(): bool
    {
        return $this->zoneCafeProche;
    }
    public function isZoneCafeProche(): bool
    {
        return $this->zoneCafeProche;
    }
    public function setZoneCafeProche(bool $zoneCafeProche): static
    {
        $this->zoneCafeProche = $zoneCafeProche;

        return $this;
    }
    /**
     * @return array<string, string>
     */
    #[Groups(['getEspacesCollaboration'])]
    public function getLinks(): array
    {
        return [
            'self' => '/api/espaces_collaboration/' . $this->getId(),
            'update' => '/api/espaces_collaboration/' . $this->getId(),
            'delete' => '/api/espaces_collaboration/' . $this->getId(),
        ];
    }

    public function getTypeAmbiance(): TypeAmbiance
    {
        return $this->typeAmbiance;
    }

    public function setTypeAmbiance(TypeAmbiance $typeAmbiance): static
    {
        $this->typeAmbiance = $typeAmbiance;

        return $this;
    }
}