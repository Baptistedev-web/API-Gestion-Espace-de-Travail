<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Repository\EspaceTravailRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EspaceTravailRepository::class)]
class EspaceTravailRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $repository = new EspaceTravailRepository($managerRegistry);

        $this->assertInstanceOf(EspaceTravailRepository::class, $repository);
    }
}