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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: StatutRepository::class)]
#[ApiResource(
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    normalizationContext: ['groups' => ['getStatuts']],
    denormalizationContext: ['groups' => ['getStatuts']],
    operations: [
        new GetCollection(
            description: 'Récupère une collection de ressources Statut.',
            normalizationContext: ['groups' => ['getStatuts']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Get(
            description: 'Récupère une ressource Statut.',
            normalizationContext: ['groups' => ['getStatuts']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            description: 'Crée une ressource Statut.',
            denormalizationContext: ['groups' => ['getStatuts']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            description: 'Remplace la ressource Statut.',
            denormalizationContext: ['groups' => ['getStatuts']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            description: 'Supprime la ressource Statut.',
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
    fields: ['libelle'],
    message: 'Ce libellé est déjà utilisé. Veuillez en choisir un autre.'
)]
class Statut
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getStatuts", "getReservations","getReservationsEspaces"])]
    private int $id = 0;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le libellé ne peut pas être vide.')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le libellé doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le libellé ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s]+$/u',
        message: 'Le libellé ne peut contenir que des lettres (y compris avec accents) et des espaces.'
    )]
    #[Groups(["getStatuts", "getReservations","getReservationsEspaces"])]
    private string $libelle = '';

    /**
     * @var Collection<int, ReservationEquipement>
     */
    #[ORM\OneToMany(targetEntity: ReservationEquipement::class, mappedBy: 'Statut', orphanRemoval: true)]
    #[Groups(["getStatuts"])]
    private Collection $reservationEquipements;

    /**
     * @var Collection<int, ReservationEspace>
     */
    #[ORM\OneToMany(targetEntity: ReservationEspace::class, mappedBy: 'Statut', orphanRemoval: true)]
    #[Groups(["getStatuts"])]
    private Collection $reservationEspaces;

    public function __construct()
    {
        $this->reservationEquipements = new ArrayCollection();
        $this->reservationEspaces = new ArrayCollection();
    }

    /**
     * @return array<string, string>
     */
    #[Groups(['getStatuts'])]
    public function getLinks(): array
    {
        return [
            'self' => '/api/statuts/' . $this->id,
            'update' => '/api/statuts/' . $this->id,
            'delete' => '/api/statuts/' . $this->id,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
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
            $reservationEquipement->setStatut($this);
        }

        return $this;
    }

    public function removeReservationEquipement(ReservationEquipement $reservationEquipement): static
    {
        if ($this->reservationEquipements->removeElement($reservationEquipement)) {
            // set the owning side to null (unless already changed)
            if ($reservationEquipement->getStatut() === $this) {
                throw new \LogicException('Impossible de supprimer le statut d\'une réservation.');
            }
        }

        return $this;
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
            $reservationEspace->setStatut($this);
        }

        return $this;
    }

    public function removeReservationEspace(ReservationEspace $reservationEspace): static
    {
        if ($this->reservationEspaces->removeElement($reservationEspace)) {
            // set the owning side to null (unless already changed)
            if ($reservationEspace->getStatut() === $this) {
                throw new \LogicException('Impossible de supprimer le statut d\'une réservation.');
            }
        }

        return $this;
    }
}

