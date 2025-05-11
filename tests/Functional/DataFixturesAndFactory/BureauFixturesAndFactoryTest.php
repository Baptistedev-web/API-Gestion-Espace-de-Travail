<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\BureauFixtures;
use App\DataFixtures\TypeBureauFixtures;
use App\Factory\BureauFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(BureauFixtures::class)]
#[CoversClass(BureauFactory::class)]
class BureauFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        BureauFactory::repository()->truncate();

        /** @var \Doctrine\Persistence\ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        // Charger d'abord les TypeBureauFixtures pour satisfaire la dépendance
        $typeBureauFixtures = new TypeBureauFixtures();
        $typeBureauFixtures->load($entityManager);

        $fixtures = new BureauFixtures();
        $fixtures->load($entityManager);
    }

    public function testBureauFixtures(): void
    {
        $this->assertCount(10, BureauFactory::repository()->findAll());
    }

    public function testBureauFactory(): void
    {
        $bureau = BureauFactory::createOne(['nom' => 'Bureau Test', 'capacite' => 5]);
        $this->assertEquals('Bureau Test', $bureau->getNom());
        $this->assertEquals(5, $bureau->getCapacite());
        $this->assertNotEmpty($bureau->getDescription());
    }
}
