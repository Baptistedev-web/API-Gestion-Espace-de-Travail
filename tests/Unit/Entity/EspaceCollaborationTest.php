<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\EspaceCollaboration;
use App\Entity\EspaceTravail;
use App\Entity\TypeAmbiance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EspaceCollaboration::class)]
class EspaceCollaborationTest extends TestCase
{
    public function testGetSetMobilierModulable(): void
    {
        $typeAmbiance = $this->createMock(TypeAmbiance::class);
        $espaceCollaboration = new EspaceCollaboration($typeAmbiance);
        $espaceCollaboration->setMobilierModulable(true);

        $this->assertTrue($espaceCollaboration->getMobilierModulable());
    }

    public function testGetSetZoneCafeProche(): void
    {
        $typeAmbiance = $this->createMock(TypeAmbiance::class);
        $espaceCollaboration = new EspaceCollaboration($typeAmbiance);
        $espaceCollaboration->setZoneCafeProche(true);

        $this->assertTrue($espaceCollaboration->getZoneCafeProche());
    }

    public function testGetSetTypeAmbiance(): void
    {
        $typeAmbiance = $this->createMock(TypeAmbiance::class);
        $espaceCollaboration = new EspaceCollaboration($typeAmbiance);

        $this->assertSame($typeAmbiance, $espaceCollaboration->getTypeAmbiance());
    }

    public function testGetLinks(): void
    {
        $typeAmbiance = $this->createMock(TypeAmbiance::class);
        $espaceCollaboration = new EspaceCollaboration($typeAmbiance);
        $reflection = new \ReflectionProperty(EspaceTravail::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($espaceCollaboration, 1);

        $expectedLinks = [
            'self' => '/api/espaces_collaboration/1',
            'update' => '/api/espaces_collaboration/1',
            'delete' => '/api/espaces_collaboration/1',
        ];

        $this->assertSame($expectedLinks, $espaceCollaboration->getLinks());
    }
}