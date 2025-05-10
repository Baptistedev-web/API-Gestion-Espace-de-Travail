<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\Bureau;
use App\Entity\EspaceTravail;
use App\Entity\TypeBureau;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Bureau::class)]
class BureauTest extends TestCase
{
    public function testGetSetNombrePoste(): void
    {
        $typeBureau = $this->createMock(TypeBureau::class);
        $bureau = new Bureau($typeBureau);
        $bureau->setNombrePoste(5);

        $this->assertSame(5, $bureau->getNombrePoste());
    }

    public function testGetSetDisponibleEnPermanent(): void
    {
        $typeBureau = $this->createMock(TypeBureau::class);
        $bureau = new Bureau($typeBureau);
        $bureau->setDisponibleEnPermanent(true);

        $this->assertTrue($bureau->getDisponibleEnPermanent());
    }

    public function testGetSetTypeBureau(): void
    {
        $typeBureau = $this->createMock(TypeBureau::class);
        $bureau = new Bureau($typeBureau);

        $this->assertSame($typeBureau, $bureau->getTypeBureau());
    }

    public function testGetLinks(): void
    {
        $typeBureau = $this->createMock(TypeBureau::class);
        $bureau = new Bureau($typeBureau);
        $reflection = new \ReflectionProperty(EspaceTravail::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($bureau, 1);

        $expectedLinks = [
            'self' => '/api/bureaux/1',
            'update' => '/api/bureaux/1',
            'delete' => '/api/bureaux/1',
        ];

        $this->assertSame($expectedLinks, $bureau->getLinks());
    }
}