<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\ReservationEquipement;
use App\Entity\Statut;
use App\Entity\User;
use App\Entity\Equipement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReservationEquipement::class)]
class ReservationEquipementTest extends TestCase
{
    public function testGetSetId(): void
    {
        $reservation = new ReservationEquipement();
        $reflection = new \ReflectionProperty(ReservationEquipement::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($reservation, 1);

        $this->assertSame(1, $reservation->getId());
    }

    public function testGetSetDateReservation(): void
    {
        $reservation = new ReservationEquipement();
        $date = new \DateTime('2023-12-01');

        $reservation->setDateReservation($date);

        $this->assertSame($date, $reservation->getDateReservation());
    }

    public function testGetSetHeureDebut(): void
    {
        $reservation = new ReservationEquipement();
        $heureDebut = new \DateTime('10:00');

        $reservation->setHeureDebut($heureDebut);

        $this->assertSame($heureDebut, $reservation->getHeureDebut());
    }

    public function testGetSetHeureFin(): void
    {
        $reservation = new ReservationEquipement();
        $heureFin = new \DateTime('12:00');

        $reservation->setHeureFin($heureFin);

        $this->assertSame($heureFin, $reservation->getHeureFin());
    }

    public function testGetSetStatut(): void
    {
        $reservation = new ReservationEquipement();
        $statut = $this->createMock(Statut::class);

        $reservation->setStatut($statut);

        $this->assertSame($statut, $reservation->getStatut());
    }

    public function testGetSetUser(): void
    {
        $reservation = new ReservationEquipement();
        $user = $this->createMock(User::class);

        $reservation->setUser($user);

        $this->assertSame($user, $reservation->getUser());
    }

    public function testGetSetEquipement(): void
    {
        $reservation = new ReservationEquipement();
        $equipement = $this->createMock(Equipement::class);

        $reservation->setEquipement($equipement);

        $this->assertSame($equipement, $reservation->getEquipement());
    }

    public function testGetLinks(): void
    {
        $reservation = new ReservationEquipement();
        $reflection = new \ReflectionProperty(ReservationEquipement::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($reservation, 1);

        $expectedLinks = [
            'self' => '/api/reservation_equipements/1',
            'update' => '/api/reservation_equipements/1',
            'delete' => '/api/reservation_equipements/1',
        ];

        $this->assertSame($expectedLinks, $reservation->getLinks());
    }
}