<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\EquipementFixtures;
use App\Factory\EquipementFactory;
use App\Entity\Equipement;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use ReflectionClass;

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
        // 10 créés manuellement + 10 via la factory
        $this->assertGreaterThanOrEqual(20, EquipementFactory::repository()->count([]));
    }

    public function testEquipementFactory(): void
    {
        $equipement = EquipementFactory::createOne(['nom' => 'Test Equipement', 'description' => 'Description test']);
        // Suppression du test d'instance, car le proxy expose déjà les getters
        $this->assertEquals('Test Equipement', $equipement->getNom());
        $this->assertEquals('Description test', $equipement->getDescription());
    }

    public function testDefaultsMethod(): void
    {
        $factory = new EquipementFactory();
        $defaults = $this->invokePrivateMethod($factory, 'defaults');
        $this->assertIsArray($defaults);
        $this->assertArrayHasKey('nom', $defaults);
        $this->assertArrayHasKey('description', $defaults);
    }

    public function testGenerateRealisticName(): void
    {
        $name = $this->invokePrivateStaticMethod(EquipementFactory::class, 'generateRealisticName');
        $this->assertIsString($name);
        $this->assertNotEmpty($name);
    }

    public function testGenerateRealisticDescription(): void
    {
        $desc = $this->invokePrivateStaticMethod(EquipementFactory::class, 'generateRealisticDescription', ['Chaise']);
        $this->assertIsString($desc);
        $this->assertStringContainsString('chaise', mb_strtolower($desc));
    }

    public function testInitializeReturnsSelf(): void
    {
        $factory = new EquipementFactory();
        $result = $this->invokeProtectedMethod($factory, 'initialize');
        $this->assertInstanceOf(EquipementFactory::class, $result);
    }

    // Méthodes utilitaires pour accéder aux méthodes privées/protégées/statics
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

    /**
     * @param class-string $class
     * @param string $method
     * @param array<int, mixed> $args
     * @return mixed
     */
    private function invokePrivateStaticMethod(string $class, string $method, array $args = []): mixed
    {
        $ref = new ReflectionClass($class);
        $m = $ref->getMethod($method);
        $m->setAccessible(true);
        return $m->invokeArgs(null, $args);
    }
}

