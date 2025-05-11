<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\ReservationEquipementFixtures;
use App\DataFixtures\StatutFixtures;
use App\Factory\ReservationEquipementFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(ReservationEquipementFixtures::class)]
#[CoversClass(ReservationEquipementFactory::class)]
class ReservationEquipementFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        ReservationEquipementFactory::repository()->truncate();

        /** @var \Doctrine\Persistence\ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        // Charger d'abord les StatutFixtures pour satisfaire la dépendance
        $statutFixtures = new StatutFixtures();
        $statutFixtures->load($entityManager);

        $fixtures = new ReservationEquipementFixtures();
        $fixtures->load($entityManager);
    }

    public function testReservationEquipementFixtures(): void
    {
        $this->assertGreaterThan(0, ReservationEquipementFactory::repository()->count([]));
    }

    public function testReservationEquipementFactory(): void
    {
        $reservation = ReservationEquipementFactory::createOne();
        $this->assertNotNull($reservation->getId());
    }
}
