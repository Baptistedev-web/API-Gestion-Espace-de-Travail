<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\ReservationEquipement;
use App\Entity\ReservationEspace;
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

    public function testAddRemoveReservationEspace(): void
    {
        $statut = new Statut();
        $reservationEspace = $this->createMock(ReservationEspace::class);

        $reservationEspace->expects($this->once())
            ->method('setStatut')
            ->with($statut);

        $statut->addReservationEspace($reservationEspace);

        $this->assertCount(1, $statut->getReservationEspaces());
        $this->assertTrue($statut->getReservationEspaces()->contains($reservationEspace));

        $reservationEspace->expects($this->once())
            ->method('getStatut')
            ->willReturn($statut);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Impossible de supprimer le statut d'une réservation.");

        $statut->removeReservationEspace($reservationEspace);

        $this->assertCount(0, $statut->getReservationEspaces());
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
    public function testRemoveReservationEquipementThrowsException(): void
    {
        $statut = new Statut();
        $reservationEquipement = $this->createMock(ReservationEquipement::class);

        $reservationEquipement->method('getStatut')->willReturn($statut);
        $statut->addReservationEquipement($reservationEquipement);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Impossible de supprimer le statut d\'une réservation.');

        $statut->removeReservationEquipement($reservationEquipement);
    }
}

