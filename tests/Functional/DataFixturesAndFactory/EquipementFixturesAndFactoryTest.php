<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\EquipementFixtures;
use App\Factory\EquipementFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(EquipementFixtures::class)]
#[CoversClass(EquipementFactory::class)]
class EquipementFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        EquipementFactory::repository()->truncate();

        /** @var \Doctrine\Persistence\ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        $fixtures = new EquipementFixtures();
        $fixtures->load($entityManager);
    }

    public function testEquipementFixtures(): void
    {
        $this->assertGreaterThanOrEqual(10, EquipementFactory::repository()->count([]));
    }

    public function testEquipementFactory(): void
    {
        $equipement = EquipementFactory::createOne(['nom' => 'Test Equipement', 'description' => 'Description test']);
        $this->assertEquals('Test Equipement', $equipement->getNom());
        $this->assertEquals('Description test', $equipement->getDescription());
    }
}
