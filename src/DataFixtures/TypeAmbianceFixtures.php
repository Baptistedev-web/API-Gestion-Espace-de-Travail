<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\TypeAmbianceFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeAmbianceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $libelles = [
            'Silencieuse',
            'Collaborative',
            'Créative',
            'Détente',
            'Dynamique',
            'Nomade',
            'Nature',
        ];

        foreach ($libelles as $libelle) {
            TypeAmbianceFactory::createOne(['libelle' => $libelle]);
        }

        $manager->flush();
    }
}
