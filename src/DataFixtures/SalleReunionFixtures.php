<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\SalleReunionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SalleReunionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de 10 salles de réunion (deuxième partie des 30 EspaceTravail)
        SalleReunionFactory::createMany(10);

        $manager->flush();
    }
}
