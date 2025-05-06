<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Equipement;
use PHPUnit\Framework\TestCase;

class EquipementTest extends TestCase
{
    public function testEquipementSettersAndGetters(): void
    {
        $equipement = new Equipement();
        $equipement->setNom('Test Equipement');
        $equipement->setDescription('Description de test');

        $this->assertSame('Test Equipement', $equipement->getNom());
        $this->assertSame('Description de test', $equipement->getDescription());
    }
    public function testGetLinks(): void
    {
        $equipement = new Equipement();
        $equipement->setNom('Test Equipement');
        $equipement->setDescription('Description de test');

        // Simuler un ID pour l'équipement
        $reflection = new \ReflectionClass($equipement);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($equipement, 1);

        $expectedLinks = [
            'self' => '/api/equipements/1',
            'update' => '/api/equipements/1',
            'delete' => '/api/equipements/1',
        ];

        $this->assertSame($expectedLinks, $equipement->getLinks());
    }
    public function testAddAndRemoveReservationEquipement(): void
    {
        $equipement = new Equipement();
        $reservation = $this->createMock(\App\Entity\ReservationEquipement::class);

        $reservation->expects($this->exactly(2))
            ->method('setEquipement')
            ->willReturnSelf();

        $reservation->expects($this->exactly(1))
            ->method('getEquipement')
            ->willReturn($equipement);

        $equipement->addReservationEquipement($reservation);
        $this->assertTrue($equipement->getReservationEquipements()->contains($reservation));

        $equipement->removeReservationEquipement($reservation);
        $this->assertFalse($equipement->getReservationEquipements()->contains($reservation));
    }
}