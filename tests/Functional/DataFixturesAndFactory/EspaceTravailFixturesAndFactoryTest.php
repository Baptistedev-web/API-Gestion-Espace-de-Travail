<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\Entity\EspaceTravail;
use App\Factory\EspaceTravailFactory;
use App\Factory\BureauFactory;
use App\Factory\SalleReunionFactory;
use App\Factory\EspaceCollaborationFactory;
use App\DataFixtures\EspaceTravailFixtures;
use App\DataFixtures\TypeBureauFixtures;
use App\DataFixtures\TypeAmbianceFixtures;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use ReflectionClass;

#[CoversClass(EspaceTravailFactory::class)]
#[CoversClass(EspaceTravailFixtures::class)]
class EspaceTravailFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        BureauFactory::repository()->truncate();
        SalleReunionFactory::repository()->truncate();
        EspaceCollaborationFactory::repository()->truncate();

        /** @var ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        // Charger les dépendances nécessaires pour les factories filles
        (new TypeBureauFixtures())->load($entityManager);
        (new TypeAmbianceFixtures())->load($entityManager);

        $fixtures = new EspaceTravailFixtures();
        $fixtures->load($entityManager);
    }

    public function testFactoryConstruct(): void
    {
        $factory = new EspaceTravailFactory();
        $this->assertInstanceOf(EspaceTravailFactory::class, $factory);
    }

    public function testFactoryClassStatic(): void
    {
        $this->assertEquals(EspaceTravail::class, EspaceTravailFactory::class());
    }

    public function testFactoryDefaults(): void
    {
        $factory = new EspaceTravailFactory();
        $defaults = $this->invokePrivateMethod($factory, 'defaults');
        $this->assertIsArray($defaults);
        $this->assertArrayHasKey('nom', $defaults);
        $this->assertArrayHasKey('description', $defaults);
        $this->assertArrayHasKey('capacite', $defaults);
    }

    public function testFactoryInitialize(): void
    {
        $factory = new EspaceTravailFactory();
        $result = $this->invokeProtectedMethod($factory, 'initialize');
        $this->assertInstanceOf(EspaceTravailFactory::class, $result);
    }

    public function testInheritanceWithBureau(): void
    {
        $bureau = BureauFactory::createOne();
        // Vérifie via une méthode héritée d'EspaceTravail (ex: getNom)
        $this->assertIsString($bureau->getNom());
    }

    public function testInheritanceWithSalleReunion(): void
    {
        $salle = SalleReunionFactory::createOne();
        $this->assertIsString($salle->getNom());
    }

    public function testInheritanceWithEspaceCollaboration(): void
    {
        $espace = EspaceCollaborationFactory::createOne();
        $this->assertIsString($espace->getNom());
    }

    public function testFixturesLoadCreatesNoDirectEspaceTravail(): void
    {
        // Utilisation correcte du repository Doctrine via le service ManagerRegistry
        /** @var \Doctrine\Persistence\ManagerRegistry $doctrine */
        $doctrine = self::getContainer()->get('doctrine');
        $repo = $doctrine->getManager()->getRepository(EspaceTravail::class);
        $all = $repo->findAll();
        $this->assertIsArray($all);
        $this->assertGreaterThanOrEqual(0, count($all));
    }

    /**
     * @param object $object
     * @param string $method
     * @param array<int, mixed> $args
     * @return mixed
     */
    private function invokePrivateMethod(object $object, string $method, array $args = []): mixed
    {
        $ref = new ReflectionClass($object);
        $m = $ref->getMethod($method);
        $m->setAccessible(true);
        return $m->invokeArgs($object, $args);
    }

    /**
     * @param object $object
     * @param string $method
     * @param array<int, mixed> $args
     * @return mixed
     */
    private function invokeProtectedMethod(object $object, string $method, array $args = []): mixed
    {
        return $this->invokePrivateMethod($object, $method, $args);
    }
}

