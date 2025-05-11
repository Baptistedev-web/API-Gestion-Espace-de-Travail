<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\TypeAmbianceFixtures;
use App\Factory\TypeAmbianceFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(TypeAmbianceFixtures::class)]
#[CoversClass(TypeAmbianceFactory::class)]
class TypeAmbianceFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        TypeAmbianceFactory::repository()->truncate();

        /** @var \Doctrine\Persistence\ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        $typeAmbianceFixtures = new TypeAmbianceFixtures();
        $typeAmbianceFixtures->load($entityManager);
    }

    public function testTypeAmbianceFixtures(): void
    {
        $this->assertCount(7, TypeAmbianceFactory::repository()->findAll());

        $libelles = [
            'Silencieuse',
            'Collaborative',
            'Créative',
            'Détente',
            'Dynamique',
            'Nomade',
            'Nature',
        ];

        foreach ($libelles as $libelle) {
            $typeAmbiance = TypeAmbianceFactory::repository()->findOneBy(['libelle' => $libelle]);
            $this->assertNotNull($typeAmbiance, "Le type d'ambiance '$libelle' doit exister.");
        }
    }

    public function testTypeAmbianceFactory(): void
    {
        $typeAmbiance = TypeAmbianceFactory::createOne(['libelle' => 'Test Ambiance']);
        $this->assertEquals('Test Ambiance', $typeAmbiance->getLibelle());
    }
}
