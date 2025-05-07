<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\DataFixtures\ReservationEquipementFixtures;
use App\Entity\ReservationEquipement;
use App\Entity\Equipement;
use App\Entity\Statut;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ReservationEquipementTest extends TestCase
{
    public function testGetAndSetId(): void
    {
        $reservation = new ReservationEquipement();
        $this->assertNull($reservation->getId());
    }

    public function testGetAndSetDateReservation(): void
    {
        $reservation = new ReservationEquipement();
        $date = new \DateTime('2023-01-01');

        $reservation->setDateReservation($date);
        $this->assertSame($date, $reservation->getDateReservation());
    }

    public function testGetAndSetHeureDebut(): void
    {
        $reservation = new ReservationEquipement();
        $heureDebut = new \DateTime('10:00');

        $reservation->setHeureDebut($heureDebut);
        $this->assertSame($heureDebut, $reservation->getHeureDebut());
    }

    public function testGetAndSetHeureFin(): void
    {
        $reservation = new ReservationEquipement();
        $heureFin = new \DateTime('12:00');

        $reservation->setHeureFin($heureFin);
        $this->assertSame($heureFin, $reservation->getHeureFin());
    }

    public function testGetAndSetStatut(): void
    {
        $reservation = new ReservationEquipement();
        $statut = new Statut();

        $reservation->setStatut($statut);
        $this->assertSame($statut, $reservation->getStatut());
    }

    public function testGetAndSetUser(): void
    {
        $reservation = new ReservationEquipement();
        $user = new User();

        $reservation->setUser($user);
        $this->assertSame($user, $reservation->getUser());
    }

    public function testGetAndSetEquipement(): void
    {
        $reservation = new ReservationEquipement();
        $equipement = new Equipement();

        $reservation->setEquipement($equipement);
        $this->assertSame($equipement, $reservation->getEquipement());
    }
    public function testGetLinks(): void
    {
        $reservation = new ReservationEquipement();
        $reflection = new \ReflectionProperty(ReservationEquipement::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($reservation, 1);

        $links = $reservation->getLinks();
        $this->assertArrayHasKey('self', $links);
        $this->assertArrayHasKey('update', $links);
        $this->assertArrayHasKey('delete', $links);
        $this->assertSame('/api/reservation_equipements/1', $links['self']);
    }
    public function testGetDependencies(): void
    {
        $fixtures = new ReservationEquipementFixtures();

        $expectedDependencies = [
            \App\DataFixtures\EquipementFixtures::class,
            \App\DataFixtures\UserFixtures::class,
            \App\DataFixtures\StatutFixtures::class,
        ];

        $this->assertSame($expectedDependencies, $fixtures->getDependencies());
    }
}