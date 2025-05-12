<?php

use PHPUnit\Framework\TestCase;
use App\Factory\EquipementFactory;

class EquipementFactoryTest extends TestCase
{
    public function testDefaultMatchArmIsGenericEquipment(): void
    {
        $reflection = new \ReflectionClass(EquipementFactory::class);
        $method = $reflection->getMethod('matchCategoryName');
        $method->setAccessible(true);

        $result = $method->invoke(null, 'Catégorie inconnue');

        $this->assertEquals('Équipement générique', $result);
    }
}
