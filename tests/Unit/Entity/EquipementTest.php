<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\Equipement;
use App\Entity\ReservationEquipement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Equipement::class)]
class EquipementTest extends TestCase
{
    public function testGetSetId(): void
    {
        $equipement = new Equipement();
        $reflection = new \ReflectionProperty(Equipement::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($equipement, 1);

        $this->assertSame(1, $equipement->getId());
    }

    public function testGetSetNom(): void
    {
        $equipement = new Equipement();
        $nom = 'Projecteur';

        $equipement->setNom($nom);

        $this->assertSame($nom, $equipement->getNom());
    }

    public function testGetSetDescription(): void
    {
        $equipement = new Equipement();
        $description = 'Un projecteur haute définition.';

        $equipement->setDescription($description);

        $this->assertSame($description, $equipement->getDescription());
    }

    public function testAddRemoveReservationEquipement(): void
    {
        $equipement = new Equipement();
        $reservation = $this->createMock(ReservationEquipement::class);

        $reservation->expects($this->once())
            ->method('setEquipement')
            ->with($equipement);

        $equipement->addReservationEquipement($reservation);

        $this->assertCount(1, $equipement->getReservationEquipements());
        $this->assertTrue($equipement->getReservationEquipements()->contains($reservation));

        $equipement->removeReservationEquipement($reservation);

        $this->assertCount(0, $equipement->getReservationEquipements());
        $this->assertFalse($equipement->getReservationEquipements()->contains($reservation));
    }

    public function testGetLinks(): void
    {
        $equipement = new Equipement();
        $reflection = new \ReflectionProperty(Equipement::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($equipement, 1);

        $expectedLinks = [
            'self' => '/api/equipements/1',
            'update' => '/api/equipements/1',
            'delete' => '/api/equipements/1',
        ];

        $this->assertSame($expectedLinks, $equipement->getLinks());
    }
}