<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\EspaceCollaboration;
use App\Entity\TypeAmbiance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TypeAmbiance::class)]
class TypeAmbianceTest extends TestCase
{
    public function testGetSetId(): void
    {
        $typeAmbiance = new TypeAmbiance();
        $reflection = new \ReflectionProperty(TypeAmbiance::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($typeAmbiance, 1);

        $this->assertSame(1, $typeAmbiance->getId());
    }

    public function testGetSetLibelle(): void
    {
        $typeAmbiance = new TypeAmbiance();
        $libelle = 'Ambiance Relaxante';

        $typeAmbiance->setLibelle($libelle);

        $this->assertSame($libelle, $typeAmbiance->getLibelle());
    }

    public function testAddRemoveEspaceCollaboration(): void
    {
        $typeAmbiance = new TypeAmbiance();
        $espaceCollaboration = $this->createMock(EspaceCollaboration::class);

        $espaceCollaboration->expects($this->once())
            ->method('setTypeAmbiance')
            ->with($typeAmbiance);

        $typeAmbiance->addEspaceCollaboration($espaceCollaboration);

        $this->assertCount(1, $typeAmbiance->getEspaceCollaboration());
        $this->assertTrue($typeAmbiance->getEspaceCollaboration()->contains($espaceCollaboration));

        $anotherTypeAmbiance = $this->createMock(TypeAmbiance::class);
        $espaceCollaboration->expects($this->once())
            ->method('getTypeAmbiance')
            ->willReturn($anotherTypeAmbiance);

        $typeAmbiance->removeEspaceCollaboration($espaceCollaboration);

        $this->assertCount(0, $typeAmbiance->getEspaceCollaboration());
        $this->assertFalse($typeAmbiance->getEspaceCollaboration()->contains($espaceCollaboration));
    }

    public function testGetLinks(): void
    {
        $typeAmbiance = new TypeAmbiance();
        $reflection = new \ReflectionProperty(TypeAmbiance::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($typeAmbiance, 1);

        $expectedLinks = [
            'self' => '/api/type_ambiances/1',
            'update' => '/api/type_ambiances/1',
            'delete' => '/api/type_ambiances/1',
        ];

        $this->assertSame($expectedLinks, $typeAmbiance->getLinks());
    }
}