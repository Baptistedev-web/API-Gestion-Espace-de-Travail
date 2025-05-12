<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\ReservationEspaceFixtures;
use App\DataFixtures\StatutFixtures;
use App\DataFixtures\TypeBureauFixtures;
use App\DataFixtures\TypeAmbianceFixtures;
use App\Factory\ReservationEspaceFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(ReservationEspaceFixtures::class)]
#[CoversClass(ReservationEspaceFactory::class)]
class ReservationEspaceFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        ReservationEspaceFactory::repository()->truncate();

        /** @var \Doctrine\Persistence\ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        // Charger d'abord les fixtures nécessaires pour les entités filles
        $typeBureauFixtures = new TypeBureauFixtures();
        $typeBureauFixtures->load($entityManager);

        $typeAmbianceFixtures = new TypeAmbianceFixtures();
        $typeAmbianceFixtures->load($entityManager);

        // Charger d'abord les StatutFixtures pour satisfaire la dépendance
        $statutFixtures = new StatutFixtures();
        $statutFixtures->load($entityManager);

        $fixtures = new ReservationEspaceFixtures();
        $fixtures->load($entityManager);
    }

    public function testReservationEspaceFixtures(): void
    {
        $this->assertGreaterThan(0, ReservationEspaceFactory::repository()->count([]));
    }

    public function testReservationEspaceFactory(): void
    {
        $reservation = ReservationEspaceFactory::createOne();
        $this->assertNotNull($reservation->getId());
    }
}
