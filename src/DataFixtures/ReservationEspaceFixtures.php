<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\EspaceTravailFixtures;
use App\DataFixtures\StatutFixtures;
use App\DataFixtures\UserFixtures;
use App\Factory\ReservationEspaceFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationEspaceFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Charge les données de fixtures dans la base de données.
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        ReservationEspaceFactory::createMany(20);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            EspaceTravailFixtures::class,
            UserFixtures::class,
            StatutFixtures::class,
        ];
    }
}
