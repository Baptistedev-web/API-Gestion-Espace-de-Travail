<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Equipement;
use App\Factory\EquipementFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EquipementFixtures extends Fixture
{
    /**
     * Charge les données de fixtures dans la base de données.
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        // Création de 10 équipements
        for ($i = 1; $i <= 10; $i++) {
            $equipement = new Equipement();
            $equipement->setNom("Equipement $i");
            $equipement->setDescription("Description de l'équipement $i");
            $manager->persist($equipement);
        }

        // Utilisation de la factory pour créer des équipements réalistes
        EquipementFactory::createMany(10);

        $manager->flush();
    }
}