<?php

namespace App\Tests\Unit\DataFixtures;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use App\DataFixtures\EspaceCollaborationFixtures;
use App\DataFixtures\TypeAmbianceFixtures;

#[CoversClass(EspaceCollaborationFixtures::class)]
class EspaceCollaborationFixturesTest extends TestCase
{
    public function testGetDependenciesReturnsExpectedArray(): void
    {
        $fixtures = new EspaceCollaborationFixtures();
        $expected = [TypeAmbianceFixtures::class];
        $this->assertSame($expected, $fixtures->getDependencies());
    }
}
