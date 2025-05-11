<?php

namespace App\Tests\Unit\DataFixtures;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use App\DataFixtures\BureauFixtures;
use App\DataFixtures\TypeBureauFixtures;

#[CoversClass(BureauFixtures::class)]
class BureauFixturesTest extends TestCase
{
    public function testGetDependenciesReturnsExpectedArray(): void
    {
        $fixtures = new BureauFixtures();
        $expected = [TypeBureauFixtures::class];
        $this->assertSame($expected, $fixtures->getDependencies());
    }
}
