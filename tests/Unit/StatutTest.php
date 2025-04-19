<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Statut;
use PHPUnit\Framework\TestCase;

class StatutTest extends TestCase
{
    public function testStatutSettersAndGetters(): void
    {
        $statut = new Statut();
        $statut->setLibelle('En attente');

        $this->assertSame('En attente', $statut->getLibelle());
    }

    public function testGetLinks(): void
    {
        $statut = new Statut();
        $statut->setLibelle('En attente');

        // Simuler un ID pour le statut
        $reflection = new \ReflectionClass($statut);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($statut, 1);

        $expectedLinks = [
            'self' => '/api/statuts/1',
            'update' => '/api/statuts/1',
            'delete' => '/api/statuts/1',
        ];

        $this->assertSame($expectedLinks, $statut->getLinks());
    }
}