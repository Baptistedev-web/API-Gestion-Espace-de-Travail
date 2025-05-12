<?php

namespace App\Tests\Unit\DataFixtures;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use App\DataFixtures\ReservationEspaceFixtures;
use App\DataFixtures\EspaceTravailFixtures;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\StatutFixtures;

#[CoversClass(ReservationEspaceFixtures::class)]
class ReservationEspaceFixturesTest extends TestCase
{
    public function testGetDependenciesReturnsExpectedArray(): void
    {
        $fixtures = new ReservationEspaceFixtures();
        $expected = [
            EspaceTravailFixtures::class,
            UserFixtures::class,
            StatutFixtures::class,
        ];
        $this->assertSame($expected, $fixtures->getDependencies());
    }
}