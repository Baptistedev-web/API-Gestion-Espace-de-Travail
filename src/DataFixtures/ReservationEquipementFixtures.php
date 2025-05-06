<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\EquipementFixtures;
use App\DataFixtures\StatutFixtures;
use App\DataFixtures\UserFixtures;
use App\Factory\ReservationEquipementFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationEquipementFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Charge les données de fixtures dans la base de données.
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        ReservationEquipementFactory::createMany(20);
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            EquipementFixtures::class,
            UserFixtures::class,
            StatutFixtures::class,
        ];
    }
}