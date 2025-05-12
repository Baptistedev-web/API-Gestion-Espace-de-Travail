<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\ReservationEspace;
use App\Entity\Statut;
use App\Entity\User;
use App\Entity\EspaceTravail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReservationEspace::class)]
class ReservationEspaceTest extends TestCase
{
    public function testGetSetId(): void
    {
        $reservation = new ReservationEspace();
        $reflection = new \ReflectionProperty(ReservationEspace::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($reservation, 1);

        $this->assertSame(1, $reservation->getId());
    }

    public function testGetSetDateReservation(): void
    {
        $reservation = new ReservationEspace();
        $date = new \DateTime('2023-12-01');

        $reservation->setDateReservation($date);

        $this->assertSame($date, $reservation->getDateReservation());
    }

    public function testGetSetHeureDebut(): void
    {
        $reservation = new ReservationEspace();
        $heureDebut = new \DateTime('10:00');

        $reservation->setHeureDebut($heureDebut);

        $this->assertSame($heureDebut, $reservation->getHeureDebut());
    }

    public function testGetSetHeureFin(): void
    {
        $reservation = new ReservationEspace();
        $heureFin = new \DateTime('12:00');

        $reservation->setHeureFin($heureFin);

        $this->assertSame($heureFin, $reservation->getHeureFin());
    }

    public function testGetSetStatut(): void
    {
        $reservation = new ReservationEspace();
        $statut = $this->createMock(Statut::class);

        $reservation->setStatut($statut);

        $this->assertSame($statut, $reservation->getStatut());
    }

    public function testGetSetUser(): void
    {
        $reservation = new ReservationEspace();
        $user = $this->createMock(User::class);

        $reservation->setUser($user);

        $this->assertSame($user, $reservation->getUser());
    }

    public function testGetSetEspaceTravail(): void
    {
        $reservation = new ReservationEspace();
        $espaceTravail = $this->createMock(EspaceTravail::class);

        $reservation->setEspaceTravail($espaceTravail);

        $this->assertSame($espaceTravail, $reservation->getEspaceTravail());
    }

    public function testGetLinks(): void
    {
        $reservation = new ReservationEspace();
        $reflection = new \ReflectionProperty(ReservationEspace::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($reservation, 1);

        $expectedLinks = [
            'self' => '/api/reservation_espaces/1',
            'update' => '/api/reservation_espaces/1',
            'delete' => '/api/reservation_espaces/1',
        ];

        $this->assertSame($expectedLinks, $reservation->getLinks());
    }
}
