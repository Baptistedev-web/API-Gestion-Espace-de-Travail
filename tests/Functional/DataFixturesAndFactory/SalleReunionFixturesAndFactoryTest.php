<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\SalleReunionFixtures;
use App\Factory\SalleReunionFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(SalleReunionFixtures::class)]
#[CoversClass(SalleReunionFactory::class)]
class SalleReunionFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        SalleReunionFactory::repository()->truncate();

        /** @var \Doctrine\Persistence\ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        $salleReunionFixtures = new SalleReunionFixtures();
        $salleReunionFixtures->load($entityManager);
    }

    public function testSalleReunionFixtures(): void
    {
        $this->assertCount(10, SalleReunionFactory::repository()->findAll());
    }

    public function testSalleReunionFactory(): void
    {
        $salle = SalleReunionFactory::createOne(['nom' => 'Salle Test', 'capacite' => 12]);
        $this->assertEquals('Salle Test', $salle->getNom());
        $this->assertEquals(12, $salle->getCapacite());
    }
}
