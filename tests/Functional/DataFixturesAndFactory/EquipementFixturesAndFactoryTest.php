<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\EquipementFixtures;
use App\Factory\EquipementFactory;
use App\Entity\Equipement;
use Doctrine\Persistence\ObjectManager;
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

        /** @var ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $fixtures = new EquipementFixtures();
        $fixtures->load($entityManager);
    }

    public function testFixturesLoadCreatesExpectedEquipements(): void
    {
        // 10 manuels + 10 via factory
        $all = EquipementFactory::repository()->findAll();
        $this->assertCount(20, $all);

        // Vérifie que les équipements manuels sont bien présents
        for ($i = 1; $i <= 10; $i++) {
            $equipement = EquipementFactory::repository()->findOneBy(['nom' => "Equipement $i"]);
            $this->assertNotNull($equipement);
            $this->assertStringContainsString((string)$i, $equipement->getDescription());
        }
    }

    public function testFactoryConstruct(): void
    {
        $factory = new EquipementFactory();
        $this->assertInstanceOf(EquipementFactory::class, $factory);
    }

    public function testFactoryClassStatic(): void
    {
        $this->assertEquals(Equipement::class, EquipementFactory::class());
    }

    public function testFactoryDefaults(): void
    {
        $factory = new EquipementFactory();
        $defaults = $this->invokePrivateMethod($factory, 'defaults');
        $this->assertIsArray($defaults);
        $this->assertArrayHasKey('nom', $defaults);
        $this->assertArrayHasKey('description', $defaults);
        $this->assertIsString($defaults['nom']);
        $this->assertIsString($defaults['description']);
    }

    public function testFactoryInitialize(): void
    {
        $factory = new EquipementFactory();
        $result = $this->invokeProtectedMethod($factory, 'initialize');
        $this->assertInstanceOf(EquipementFactory::class, $result);
    }

    public function testGenerateRealisticDescriptionAllCases(): void
    {
        $cases = [
            'Chaise' => 'Une chaise ergonomique idéale pour le bureau, offrant un confort optimal.',
            'Bureau' => 'Un bureau spacieux et moderne, parfait pour travailler efficacement.',
            'Lampe' => 'Une lampe élégante qui éclaire parfaitement votre espace de travail.',
            'Écran' => 'Un écran haute résolution pour une expérience visuelle exceptionnelle.',
            'Clavier' => 'Un clavier mécanique robuste, idéal pour la saisie rapide.',
            'Souris' => 'Une souris sans fil ergonomique pour une navigation fluide.',
            'Projecteur' => 'Un projecteur haute qualité pour vos présentations en salle de réunion.',
            'Table' => 'Une table robuste et élégante, parfaite pour les réunions.',
            'Haut-parleur' => 'Un haut-parleur puissant pour une qualité sonore exceptionnelle.',
            'Tableau blanc' => 'Un tableau blanc magnétique, idéal pour les brainstormings.',
            'Télévision' => 'Une télévision 4K pour des présentations visuelles impressionnantes.',
            'Canapé' => 'Un canapé confortable pour vos espaces de collaboration.',
            'Table basse' => 'Une table basse moderne pour vos espaces de détente.',
            'Station de recharge' => 'Une station de recharge pratique pour vos appareils électroniques.',
            'Panneau acoustique' => 'Un panneau acoustique pour réduire le bruit ambiant.',
            'Tabouret' => 'Un tabouret design et pratique pour vos espaces de travail.',
        ];
        foreach ($cases as $nom => $expected) {
            $desc = $this->invokePrivateStaticMethod(EquipementFactory::class, 'generateRealisticDescription', [$nom]);
            $this->assertEquals($expected, $desc);
        }
    }

    public function testGenerateRealisticDescriptionDefault(): void
    {
        $desc = $this->invokePrivateStaticMethod(EquipementFactory::class, 'generateRealisticDescription', ['Inconnu']);
        $this->assertIsString($desc);
        $this->assertNotEmpty($desc);
    }

    // --- OUTILS REFLEXION ---

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
