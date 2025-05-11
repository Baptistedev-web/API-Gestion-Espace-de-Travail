<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\TypeBureauFixtures;
use App\Factory\TypeBureauFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Bridge\Doctrine\ManagerRegistry;

#[CoversClass(TypeBureauFixtures::class)]
#[CoversClass(TypeBureauFactory::class)]
class TypeBureauFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        TypeBureauFactory::repository()->truncate();

        /** @var \Doctrine\Persistence\ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        $typeBureauFixtures = new TypeBureauFixtures();
        $typeBureauFixtures->load($entityManager);
    }

    public function testTypeBureauFixtures(): void
    {
        $this->assertCount(7, TypeBureauFactory::repository()->findAll());

        $libelles = [
            'Bureau individuel',
            'Bureau partagé',
            'Salle de réunion',
            'Bureau fermé',
            'Espace détente',
            'Espace formation',
            'Phone box',
        ];

        foreach ($libelles as $libelle) {
            $typeBureau = TypeBureauFactory::repository()->findOneBy(['libelle' => $libelle]);
            $this->assertNotNull($typeBureau, "Le type de bureau '$libelle' doit exister.");
        }
    }

    public function testTypeBureauFactory(): void
    {
        $typeBureau = TypeBureauFactory::createOne(['libelle' => 'Test Bureau']);
        $this->assertEquals('Test Bureau', $typeBureau->getLibelle());
    }
}
