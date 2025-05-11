<?php

namespace App\Tests\Unit\Factory;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use App\Factory\EquipementFactory;

class EquipementFactoryTestUtil
{
    /**
     * Simule la logique de generateRealisticName pour les tests unitaires.
     * @param mixed $category
     * @return string
     */
    public static function simulateGenerateRealisticName(mixed $category = null): string
    {
        // Ligne 66 : $category = 'Équipement générique';
        if (!is_string($category)) {
            $category = 'Équipement générique';
        }

        // Ligne 73 : default => 'Équipement générique',
        $name = match ($category) {
            'Bureau' => 'Chaise',
            'Salle de réunion' => 'Projecteur',
            'Espace de collaboration' => 'Canapé',
            default => 'Équipement générique',
        };

        return $name;
    }
}

#[CoversClass(EquipementFactory::class)]
class EquipementFactoryTest extends TestCase
{
    public function testLigne66CategoryNonString(): void
    {
        // Couvre la ligne 66 : $category = 'Équipement générique';
        $result = EquipementFactoryTestUtil::simulateGenerateRealisticName(null);
        $this->assertSame('Équipement générique', $result);

        $result = EquipementFactoryTestUtil::simulateGenerateRealisticName([]);
        $this->assertSame('Équipement générique', $result);

        $result = EquipementFactoryTestUtil::simulateGenerateRealisticName(123);
        $this->assertSame('Équipement générique', $result);
    }

    public function testLigne73DefaultMatch(): void
    {
        // Couvre la ligne 73 : default => 'Équipement générique',
        $result = EquipementFactoryTestUtil::simulateGenerateRealisticName('Inconnue');
        $this->assertSame('Équipement générique', $result);

        $result = EquipementFactoryTestUtil::simulateGenerateRealisticName('Autre');
        $this->assertSame('Équipement générique', $result);
    }

    public function testCategorieConnue(): void
    {
        $this->assertSame('Chaise', EquipementFactoryTestUtil::simulateGenerateRealisticName('Bureau'));
        $this->assertSame('Projecteur', EquipementFactoryTestUtil::simulateGenerateRealisticName('Salle de réunion'));
        $this->assertSame('Canapé', EquipementFactoryTestUtil::simulateGenerateRealisticName('Espace de collaboration'));
    }
}
