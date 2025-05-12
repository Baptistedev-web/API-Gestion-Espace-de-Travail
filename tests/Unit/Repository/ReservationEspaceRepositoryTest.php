<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Repository\ReservationEspaceRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReservationEspaceRepository::class)]
class ReservationEspaceRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $repository = new ReservationEspaceRepository($managerRegistry);

        $this->assertInstanceOf(ReservationEspaceRepository::class, $repository);
    }
}