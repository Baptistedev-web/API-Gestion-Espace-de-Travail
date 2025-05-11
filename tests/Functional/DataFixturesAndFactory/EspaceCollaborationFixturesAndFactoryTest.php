<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\EspaceCollaborationFixtures;
use App\DataFixtures\TypeAmbianceFixtures;
use App\Factory\EspaceCollaborationFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(EspaceCollaborationFixtures::class)]
#[CoversClass(EspaceCollaborationFactory::class)]
class EspaceCollaborationFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        EspaceCollaborationFactory::repository()->truncate();

        /** @var \Doctrine\Persistence\ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        // Charger d'abord les TypeAmbianceFixtures pour satisfaire la dépendance
        $typeAmbianceFixtures = new TypeAmbianceFixtures();
        $typeAmbianceFixtures->load($entityManager);

        $fixtures = new EspaceCollaborationFixtures();
        $fixtures->load($entityManager);
    }

    public function testEspaceCollaborationFixtures(): void
    {
        $this->assertCount(10, EspaceCollaborationFactory::repository()->findAll());
    }

    public function testEspaceCollaborationFactory(): void
    {
        $espace = EspaceCollaborationFactory::createOne(['nom' => 'Espace Test', 'capacite' => 25]);
        $this->assertEquals('Espace Test', $espace->getNom());
        $this->assertEquals(25, $espace->getCapacite());
        $this->assertNotEmpty($espace->getDescription());
    }
}
