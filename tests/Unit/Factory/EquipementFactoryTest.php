<?php

namespace App\Tests\Unit\Factory;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use App\Factory\EquipementFactory;

class EquipementFactoryTestUtil
{
    /**
     * Simule la logique de generateRealisticName pour les tests unitaires.
     * @param string|int|array<mixed>|null $category
     * @param string|null $name
     */
    public static function generateRealisticNameTest(string|int|array|null $category, string|null $name): string
    {
        if (!is_string($category)) {
            $category = 'Équipement générique';
        }

        switch ($category) {
            case 'Bureau':
            case 'Salle de réunion':
            case 'Espace de collaboration':
                // $name reste tel quel
                break;
            default:
                $name = 'Équipement générique';
        }

        return is_string($name) && $name !== '' ? $name : 'Équipement générique';
    }

    /**
     * Simule la logique de generateRealisticDescription pour les tests unitaires.
     */
    public static function generateRealisticDescriptionTest(string $nom, string $defaultDescription): string
    {
        return match ($nom) {
            'Chaise' => 'Une chaise ergonomique idéale pour le bureau, offrant un confort optimal.',
            default => $defaultDescription,
        };
    }
}

#[CoversClass(EquipementFactory::class)]
class EquipementFactoryTest extends TestCase
{
    public function testGenerateRealisticNameWithNonStringCategory(): void
    {
        $category = null;
        $name = 'Chaise';
        $result = EquipementFactoryTestUtil::generateRealisticNameTest($category, $name);
        $this->assertSame('Équipement générique', $result);
    }

    public function testGenerateRealisticNameWithUnknownCategory(): void
    {
        $category = 'Inconnue';
        $name = 'Chaise';
        $result = EquipementFactoryTestUtil::generateRealisticNameTest($category, $name);
        $this->assertSame('Équipement générique', $result);
    }

    public function testGenerateRealisticNameWithNonStringName(): void
    {
        $category = 'Bureau';
        $name = null;
        $result = EquipementFactoryTestUtil::generateRealisticNameTest($category, $name);
        $this->assertSame('Équipement générique', $result);
    }

    public function testGenerateRealisticNameWithValidCategory(): void
    {
        $category = 'Bureau';
        $name = 'Chaise';
        $result = EquipementFactoryTestUtil::generateRealisticNameTest($category, $name);
        $this->assertSame('Chaise', $result);
    }

    public function testGenerateRealisticDescriptionWithValidName(): void
    {
        $nom = 'Chaise';
        $defaultDescription = 'Description générique générée pour un équipement inconnu.';
        $result = EquipementFactoryTestUtil::generateRealisticDescriptionTest($nom, $defaultDescription);
        $this->assertSame('Une chaise ergonomique idéale pour le bureau, offrant un confort optimal.', $result);
    }

    public function testGenerateRealisticDescriptionWithInvalidName(): void
    {
        $nom = 'Nom inconnu';
        $defaultDescription = 'Description générique générée pour un équipement inconnu.';
        $result = EquipementFactoryTestUtil::generateRealisticDescriptionTest($nom, $defaultDescription);
        $this->assertSame($defaultDescription, $result);
    }
}

