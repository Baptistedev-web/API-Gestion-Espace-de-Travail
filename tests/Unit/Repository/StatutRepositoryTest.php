<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Repository\StatutRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StatutRepository::class)]
class StatutRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $repository = new StatutRepository($managerRegistry);

        $this->assertInstanceOf(StatutRepository::class, $repository);
    }
}