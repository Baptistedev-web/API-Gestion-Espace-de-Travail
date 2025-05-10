<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\SalleReunion;
use App\Entity\EspaceTravail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SalleReunion::class)]
class SalleReunionTest extends TestCase
{
    public function testGetSetEquipementVisioConference(): void
    {
        $salleReunion = new SalleReunion('Salle A', 'Description de la salle', 10);
        $salleReunion->setEquipementVisioConference(true);

        $this->assertTrue($salleReunion->getEquipementVisioConference());
    }

    public function testGetSetReservationObligatoire(): void
    {
        $salleReunion = new SalleReunion('Salle B', 'Description de la salle', 20);
        $salleReunion->setReservationObligatoire(true);

        $this->assertTrue($salleReunion->getReservationObligatoire());
    }

    public function testGetLinks(): void
    {
        $salleReunion = new SalleReunion('Salle C', 'Description de la salle', 30);
        $reflection = new \ReflectionProperty(EspaceTravail::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($salleReunion, 1);

        $expectedLinks = [
            'self' => '/api/salles_reunion/1',
            'update' => '/api/salles_reunion/1',
            'delete' => '/api/salles_reunion/1',
        ];

        $this->assertSame($expectedLinks, $salleReunion->getLinks());
    }
}