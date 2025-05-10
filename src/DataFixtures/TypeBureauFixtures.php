<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\TypeBureauFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TypeBureauFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $libelles = [
            'Bureau individuel',
            'Bureau partagé',
            'Salle de réunion',
            'Bureau fermé',
            'Espace détente',
            'Espace formation',
            'Phone box',
        ];

        foreach ($libelles as $libelle) {
            TypeBureauFactory::createOne(['libelle' => $libelle]);
        }

        $manager->flush();
    }
}
