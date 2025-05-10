<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\EspaceCollaborationFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\TypeAmbianceFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EspaceCollaborationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Création de 10 espaces de collaboration (troisième partie des 30 EspaceTravail)
        EspaceCollaborationFactory::createMany(10);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TypeAmbianceFixtures::class,
        ];
    }
}
