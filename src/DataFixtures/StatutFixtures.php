<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\StatutFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatutFixtures extends Fixture
{
    /**
     * Charge les données de fixtures dans la base de données.
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        // Création de 4 statuts
        StatutFactory::createDefaultStatuts();
    }
}
