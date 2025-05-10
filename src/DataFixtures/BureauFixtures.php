<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\BureauFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\TypeBureauFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BureauFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Création de 10 bureaux (première partie des 30 EspaceTravail)
        BureauFactory::createMany(10);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TypeBureauFixtures::class,
        ];
    }
}
