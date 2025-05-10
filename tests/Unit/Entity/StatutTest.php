<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\ReservationEquipement;
use App\Entity\Statut;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Statut::class)]
class StatutTest extends TestCase
{
    public function testGetSetId(): void
    {
        $statut = new Statut();
        $reflection = new \ReflectionProperty(Statut::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($statut, 1);

        $this->assertSame(1, $statut->getId());
    }

    public function testGetSetLibelle(): void
    {
        $statut = new Statut();
        $libelle = 'Statut Actif';

        $statut->setLibelle($libelle);

        $this->assertSame($libelle, $statut->getLibelle());
    }

    public function testAddRemoveReservationEquipement(): void
    {
        $statut = new Statut();
        $reservationEquipement = $this->createMock(ReservationEquipement::class);

        $reservationEquipement->expects($this->once())
            ->method('setStatut')
            ->with($statut);

        $statut->addReservationEquipement($reservationEquipement);

        $this->assertCount(1, $statut->getReservationEquipements());
        $this->assertTrue($statut->getReservationEquipements()->contains($reservationEquipement));

        $anotherStatut = $this->createMock(Statut::class);
        $reservationEquipement->expects($this->once())
            ->method('getStatut')
            ->willReturn($anotherStatut);

        $statut->removeReservationEquipement($reservationEquipement);

        $this->assertCount(0, $statut->getReservationEquipements());
        $this->assertFalse($statut->getReservationEquipements()->contains($reservationEquipement));
    }

    public function testGetLinks(): void
    {
        $statut = new Statut();
        $reflection = new \ReflectionProperty(Statut::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($statut, 1);

        $expectedLinks = [
            'self' => '/api/statuts/1',
            'update' => '/api/statuts/1',
            'delete' => '/api/statuts/1',
        ];

        $this->assertSame($expectedLinks, $statut->getLinks());
    }
}