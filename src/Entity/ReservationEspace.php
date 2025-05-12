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
use App\Repository\ReservationEspaceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ReservationEspaceRepository::class)]
#[ORM\UniqueConstraint(
    name: "reservation_espace_unique",
    columns: ["user_id", "espace_travail_id", "date_reservation", "heure_debut"]
)]
#[ApiResource(
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    normalizationContext: ['groups' => ['getReservationsEspaces']],
    denormalizationContext: ['groups' => ['getReservationsEspaces']],
    operations: [
        new GetCollection(
            description: "Récupère une collection de ressources Réservation d'Espace.",
            normalizationContext: ['groups' => ['getReservationsEspaces']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Vous devez être administrateur pour accéder à cette ressource.",
        ),
        new Get(
            description: "Récupère une ressource Réservation d'Espace.",
            normalizationContext: ['groups' => ['getReservationsEspaces']],
            security: "is_granted('ROLE_USER') and object.getUser() == user",
            securityMessage: "Vous ne pouvez accéder qu'à vos propres réservations."
        ),
        new Post(
            description: "Crée une ressource Réservation d'Espace.",
            denormalizationContext: ['groups' => ['getReservationsEspaces']],
            security: "is_granted('ROLE_USER')"
        ),
        new Put(
            description: "Remplace la ressource Réservation d'Espace.",
            denormalizationContext: ['groups' => ['getReservationsEspaces']],
            security: "is_granted('ROLE_USER') and object.getUser() == user",
            securityMessage: "Vous ne pouvez modifier que vos propres réservations.",
        ),
        new Delete(
            description: "Supprime la ressource Réservation d'Espace.",
            security: "is_granted('ROLE_USER') and object.getUser() == user",
        ),
    ],
    formats: ['jsonld', 'json'],
    cacheHeaders: [
        'max_age' => 3600, // Cache pour 1 heure
        'shared_max_age' => 3600,
        'vary' => ['Authorization', 'Accept-Language'],
    ],
)]
class ReservationEspace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['getReservationsEspaces'])]
    private int $id = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de réservation ne doit pas être vide.")]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: "La date de réservation doit être aujourd'hui ou dans le futur."
    )]
    #[Groups(['getReservationsEspaces'])]
    private \DateTime $dateReservation;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotNull(message: "L'heure de début ne doit pas être vide.")]
    #[Assert\LessThan(
        propertyPath: 'heureFin',
        message: "L'heure de début doit être inférieure à l'heure de fin."
    )]
    #[Groups(['getReservationsEspaces'])]
    private \DateTime $heureDebut;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotNull(message: "L'heure de fin ne doit pas être vide.")]
    #[Assert\GreaterThan(
        propertyPath: 'heureDebut',
        message: "L'heure de fin doit être supérieure à l'heure de début."
    )]
    #[Groups(['getReservationsEspaces'])]
    private \DateTime $heureFin;

    #[ORM\ManyToOne(inversedBy: 'reservationEspaces')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le statut ne doit pas être null.")]
    #[Groups(['getReservationsEspaces'])]
    private ?Statut $Statut = null;

    #[ORM\ManyToOne(inversedBy: 'reservationEspaces')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'utilisateur ne doit pas être null.")]
    #[Groups(['getReservationsEspaces'])]
    private ?User $User = null;

    #[ORM\ManyToOne(inversedBy: 'reservationEspaces')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'espace de travail ne doit pas être null.")]
    #[Groups(['getReservationsEspaces'])]
    private ?EspaceTravail $EspaceTravail = null;

    public function __construct()
    {
        $this->dateReservation = new \DateTime();
        $this->heureDebut = new \DateTime();
        $this->heureFin = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDateReservation(): \DateTime
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTime $dateReservation): static
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getHeureDebut(): \DateTime
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(\DateTime $heureDebut): static
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): \DateTime
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

    public function getEspaceTravail(): ?EspaceTravail
    {
        return $this->EspaceTravail;
    }

    public function setEspaceTravail(?EspaceTravail $EspaceTravail): static
    {
        $this->EspaceTravail = $EspaceTravail;

        return $this;
    }

    #[Assert\Callback]
    public function validateNoOverlap(ExecutionContextInterface $context): void
    {
        if ($this->EspaceTravail === null) {
            return;
        }
        $reservations = $this->EspaceTravail->getReservationEspaces();
        foreach ($reservations as $reservation) {
            if ($reservation === $this) {
                continue;
            }
            if ($reservation->getDateReservation()->format('Y-m-d') !== $this->dateReservation->format('Y-m-d')) {
                continue;
            }
            // Chevauchement horaire
            if (
                ($this->heureDebut < $reservation->getHeureFin()) &&
                ($this->heureFin > $reservation->getHeureDebut())
            ) {
                $context->buildViolation("Il existe déjà une réservation pour cet espace de travail sur ce créneau horaire.")
                    ->atPath('heureDebut')
                    ->addViolation();
                break;
            }
        }
    }

    /**
     * @return array<string, string>
     */
    #[Groups(['getReservationsEspaces'])]
    public function getLinks(): array
    {
        return [
            'self' => "/api/reservation_espaces/".$this->id,
            'update' => "/api/reservation_espaces/".$this->id,
            'delete' => "/api/reservation_espaces/".$this->id,
        ];
    }
}
