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
            description: 'Récupère une collection de ressources SalleReunion.',
            normalizationContext: ['groups' => ['getSallesReunion']]
        ),
        new Get(
            description: 'Récupère une ressource SalleReunion.',
            normalizationContext: ['groups' => ['getSallesReunion']]
        ),
        new Post(
            description: 'Crée une ressource SalleReunion.',
            denormalizationContext: ['groups' => ['getSallesReunion']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            description: 'Met à jour une ressource SalleReunion.',
            denormalizationContext: ['groups' => ['getSallesReunion']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            description: 'Supprime une ressource SalleReunion.',
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    normalizationContext: ['groups' => ['getSallesReunion']],
    denormalizationContext: ['groups' => ['getSallesReunion']],
    formats: ['jsonld', 'json'],
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
)]
class SalleReunion extends EspaceTravail
{
    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull(message: "Veuillez indiquer si la salle est équipée pour la visioconférence.")]
    #[Groups(["getSallesReunion", "getEspacesTravail"])]
    private bool $equipementVisioConference = false;

    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull(message: "Veuillez indiquer si la réservation est obligatoire.")]
    #[Groups(["getSallesReunion", "getEspacesTravail"])]
    private bool $reservationObligatoire = false;

    public function getEquipementVisioConference(): bool
    {
        return $this->equipementVisioConference;
    }
    public function setEquipementVisioConference(bool $equipementVisioConference): static
    {
        $this->equipementVisioConference = $equipementVisioConference;

        return $this;
    }
    public function getReservationObligatoire(): bool
    {
        return $this->reservationObligatoire;
    }
    public function setReservationObligatoire(bool $reservationObligatoire): static
    {
        $this->reservationObligatoire = $reservationObligatoire;

        return $this;
    }
    /**
     * @return array<string, string>
     */
    #[Groups(['getSallesReunion'])]
    public function getLinks(): array
    {
        return [
            'self' => '/api/salles_reunion/' . $this->getId(),
            'update' => '/api/salles_reunion/' . $this->getId(),
            'delete' => '/api/salles_reunion/' . $this->getId(),
        ];
    }
}