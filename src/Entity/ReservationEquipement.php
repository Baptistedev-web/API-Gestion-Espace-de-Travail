<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Repository\ReservationEquipementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReservationEquipementRepository::class)]
#[ORM\UniqueConstraint(
    name: "reservation_unique",
    columns: ["user_id", "equipement_id", "date_reservation", "heure_debut"]
)]
#[ApiResource(
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    normalizationContext: ['groups' => ['getReservations']],
    denormalizationContext: ['groups' => ['getReservations']],
    operations: [
        new GetCollection(
            description: "Récupère une collection de ressources Réservation d'Équipement.",
            normalizationContext: ['groups' => ['getReservations']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Vous devez être administrateur pour accéder à cette ressource.",
        ),
        new Get(
            description: "Récupère une ressource Réservation d'Équipement.",
            normalizationContext: ['groups' => ['getReservations']],
            security: "is_granted('ROLE_USER') and object.getUser() == user",
            securityMessage: "Vous ne pouvez accéder qu'à vos propres réservations."
        ),
        new Post(
            description: "Crée une ressource Réservation d'Équipement.",
            denormalizationContext: ['groups' => ['getReservations']],
            security: "is_granted('ROLE_USER')"
        ),
        new Put(
            description: "Remplace la ressource Réservation d'Équipement.",
            denormalizationContext: ['groups' => ['getReservations']],
            security: "is_granted('ROLE_USER') and object.getUser() == user",
            securityMessage: "Vous ne pouvez modifier que vos propres réservations.",

        ),
        new Delete(
            description: "Supprime la ressource Réservation d'Équipement.",
            security: "is_granted('ROLE_USER') and object.getUSer() == user",
        ),
    ],
    formats: ['jsonld', 'json'],
    cacheHeaders: [
        'max_age' => 3600, // Cache pour 1 heure
        'shared_max_age' => 3600,
        'vary' => ['Authorization', 'Accept-Language'],
    ],
)]
class ReservationEquipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["getReservations"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservationEquipements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'utilisateur ne doit pas être nul.")]
    #[Groups(["getReservations"])]
    private ?User $User = null;
    
    #[ORM\ManyToOne(inversedBy: 'reservationEquipements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'équipement ne doit pas être nul.")]
    #[Groups(["getReservations"])]
    private ?Equipement $Equipement = null;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de réservation ne doit pas être vide.")]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: "La date de réservation doit être aujourd'hui ou dans le futur."
    )]
    #[Groups(["getReservations"])]
    private ?\DateTime $dateReservation = null;
    
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotNull(message: "L'heure de début ne doit pas être vide.")]
    #[Assert\LessThan(
        propertyPath: 'heureFin',
        message: "L'heure de début doit être inférieure à l'heure de fin."
    )]
    #[Groups(["getReservations"])]
    private ?\DateTime $heureDebut = null;
    
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotNull(message: "L'heure de fin ne doit pas être vide.")]
    #[Assert\GreaterThan(
        propertyPath: 'heureDebut',
        message: "L'heure de fin doit être supérieure à l'heure de début."
    )]
    #[Groups(["getReservations"])]
    private ?\DateTime $heureFin = null;
    
    #[ORM\ManyToOne(inversedBy: 'reservationEquipements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le statut ne doit pas être nul.")]
    #[Groups(["getReservations"])]
    private ?Statut $Statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReservation(): ?\DateTime
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTime $dateReservation): static
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getHeureDebut(): ?\DateTime
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(\DateTime $heureDebut): static
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?\DateTime
    {
        return $this->heureFin;
    }

    public function setHeureFin(\DateTime $heureFin): static
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    public function getStatut(): ?Statut
    {
        return $this->Statut;
    }

    public function setStatut(?Statut $Statut): static
    {
        $this->Statut = $Statut;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getEquipement(): ?Equipement
    {
        return $this->Equipement;
    }

    public function setEquipement(?Equipement $Equipement): static
    {
        $this->Equipement = $Equipement;

        return $this;
    }
    /**
     * @return array<string, string>
     */
    #[Groups(['getReservations'])]
    public function getLinks(): array
    {
        return [
            'self' => "/api/reservation_equipements/".$this->id,
            'update' => "/api/reservation_equipements/".$this->id,
            'delete' => "/api/reservation_equipements/".$this->id,
        ];
    }
}
