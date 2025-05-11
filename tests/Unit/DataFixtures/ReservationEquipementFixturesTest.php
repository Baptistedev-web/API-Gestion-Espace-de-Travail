<?php

namespace App\Tests\Unit\DataFixtures;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use App\DataFixtures\ReservationEquipementFixtures;
use App\DataFixtures\EquipementFixtures;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\StatutFixtures;

#[CoversClass(ReservationEquipementFixtures::class)]
class ReservationEquipementFixturesTest extends TestCase
{
    public function testGetDependenciesReturnsExpectedArray(): void
    {
        $fixtures = new ReservationEquipementFixtures();
        $expected = [
            EquipementFixtures::class,
            UserFixtures::class,
            StatutFixtures::class,
        ];
        $this->assertSame($expected, $fixtures->getDependencies());
    }
}
