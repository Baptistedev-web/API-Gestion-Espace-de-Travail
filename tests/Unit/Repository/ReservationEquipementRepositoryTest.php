<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Repository\ReservationEquipementRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReservationEquipementRepository::class)]
class ReservationEquipementRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $repository = new ReservationEquipementRepository($managerRegistry);

        $this->assertInstanceOf(ReservationEquipementRepository::class, $repository);
    }
}
