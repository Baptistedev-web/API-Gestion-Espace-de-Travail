<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\StatutFixtures;
use App\Factory\StatutFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(StatutFixtures::class)]
#[CoversClass(StatutFactory::class)]
class StatutFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        StatutFactory::repository()->truncate();

        /** @var \Doctrine\Persistence\ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        $statutFixtures = new StatutFixtures();
        $statutFixtures->load($entityManager);
    }

    public function testStatutFixtures(): void
    {
        $this->assertCount(4, StatutFactory::repository()->findAll());

        $libelles = [
            'En attente',
            'Confirmée',
            'Annulée',
            'Terminée',
        ];

        foreach ($libelles as $libelle) {
            $statut = StatutFactory::repository()->findOneBy(['libelle' => $libelle]);
            $this->assertNotNull($statut, "Le statut '$libelle' doit exister.");
        }
    }

    public function testStatutFactory(): void
    {
        $statut = StatutFactory::createOne(['libelle' => 'Test Statut']);
        $this->assertEquals('Test Statut', $statut->getLibelle());
    }
}
