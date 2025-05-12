<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\EspaceTravail;
use App\Entity\ReservationEspace;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EspaceTravail::class)]
class EspaceTravailTest extends TestCase
{
    public function testGetSetId(): void
    {
        $espaceTravail = new EspaceTravail('Nom', 'Description', 10);
        $reflection = new \ReflectionProperty(EspaceTravail::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($espaceTravail, 1);

        $this->assertSame(1, $espaceTravail->getId());
    }

    public function testGetSetNom(): void
    {
        $espaceTravail = new EspaceTravail('Nom', 'Description', 10);
        $espaceTravail->setNom('Nouveau Nom');

        $this->assertSame('Nouveau Nom', $espaceTravail->getNom());
    }

    public function testGetSetDescription(): void
    {
        $espaceTravail = new EspaceTravail('Nom', 'Description', 10);
        $espaceTravail->setDescription('Nouvelle Description');

        $this->assertSame('Nouvelle Description', $espaceTravail->getDescription());
    }

    public function testGetSetCapacite(): void
    {
        $espaceTravail = new EspaceTravail('Nom', 'Description', 10);
        $espaceTravail->setCapacite(20);

        $this->assertSame(20, $espaceTravail->getCapacite());
    }

    public function testGetLinks(): void
    {
        $espaceTravail = new EspaceTravail('Nom', 'Description', 10);
        $reflection = new \ReflectionProperty(EspaceTravail::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($espaceTravail, 1);

        $expectedLinks = [
            'self' => '/api/espaces_travail/1',
            'update' => '/api/espaces_travail/1',
            'delete' => '/api/espaces_travail/1',
        ];

        $this->assertSame($expectedLinks, $espaceTravail->getLinks());
    }

    public function testAddAndRemoveReservationEspace(): void
    {
        $espaceTravail = new EspaceTravail('Nom', 'Description', 10);
        $reservation = $this->createMock(ReservationEspace::class);

        $reservation->expects($this->once())
            ->method('setEspaceTravail')
            ->with($espaceTravail);

        $espaceTravail->addReservationEspace($reservation);

        $this->assertCount(1, $espaceTravail->getReservationEspaces());
        $this->assertTrue($espaceTravail->getReservationEspaces()->contains($reservation));

        $reservation->expects($this->once())
            ->method('getEspaceTravail')
            ->willReturn($espaceTravail);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Impossible de supprimer l'espace de travail d'une réservation.");

        $espaceTravail->removeReservationEspace($reservation);
    }
}

