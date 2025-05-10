<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Repository\TypeAmbianceRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TypeAmbianceRepository::class)]
class TypeAmbianceRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $repository = new TypeAmbianceRepository($managerRegistry);

        $this->assertInstanceOf(TypeAmbianceRepository::class, $repository);
    }
}