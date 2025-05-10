<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Repository\EquipementRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EquipementRepository::class)]
class EquipementRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $repository = new EquipementRepository($managerRegistry);

        $this->assertInstanceOf(EquipementRepository::class, $repository);
    }
}