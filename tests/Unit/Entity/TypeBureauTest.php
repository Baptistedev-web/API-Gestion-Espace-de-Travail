<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\Bureau;
use App\Entity\TypeBureau;
use PHPUnit\Framework\Attributes\CoversClass; // Correction de l'import
use PHPUnit\Framework\TestCase;

#[CoversClass(TypeBureau::class)]
class TypeBureauTest extends TestCase
{
    public function testGetSetId(): void
    {
        $typeBureau = new TypeBureau();
        $reflection = new \ReflectionProperty(TypeBureau::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($typeBureau, 1);

        $this->assertSame(1, $typeBureau->getId());
    }
    public function testGetSetLibelle(): void
    {
        $typeBureau = new TypeBureau();
        $libelle = 'Bureau Administratif';

        $typeBureau->setLibelle($libelle);

        $this->assertSame($libelle, $typeBureau->getLibelle());
    }
    public function testAddRemoveBureau(): void
    {
        $typeBureau = new TypeBureau();
        $bureau = $this->createMock(Bureau::class);

        $bureau->expects($this->once())
            ->method('setTypeBureau')
            ->with($typeBureau);

        $typeBureau->addBureau($bureau);

        $this->assertCount(1, $typeBureau->getBureau());
        $this->assertTrue($typeBureau->getBureau()->contains($bureau));

        $anotherTypeBureau = $this->createMock(TypeBureau::class);
        $bureau->expects($this->once())
            ->method('getTypeBureau')
            ->willReturn($anotherTypeBureau);

        $typeBureau->removeBureau($bureau);

        $this->assertCount(0, $typeBureau->getBureau());
        $this->assertFalse($typeBureau->getBureau()->contains($bureau));
    }
    public function testGetLinks(): void
    {
        $typeBureau = new TypeBureau();
        $reflection = new \ReflectionProperty(TypeBureau::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($typeBureau, 1);

        $expectedLinks = [
            'self' => '/api/type_bureaux/1',
            'update' => '/api/type_bureaux/1',
            'delete' => '/api/type_bureaux/1',
        ];

        $this->assertSame($expectedLinks, $typeBureau->getLinks());
    }
    public function testRemoveBureauThrowsException(): void
    {
        $typeBureau = new TypeBureau();
        $bureau = $this->createMock(Bureau::class);

        $bureau->method('getTypeBureau')->willReturn($typeBureau);
        $typeBureau->addBureau($bureau);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Impossible de supprimer le type de bureau car il est encore utilisé par un bureau.');

        $typeBureau->removeBureau($bureau);
    }
}

