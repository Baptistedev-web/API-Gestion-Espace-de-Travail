<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Repository\TypeBureauRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
#[CoversClass(TypeBureauRepository::class)]
class TypeBureauRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $repository = new TypeBureauRepository($managerRegistry);

        $this->assertInstanceOf(TypeBureauRepository::class, $repository);
    }
}