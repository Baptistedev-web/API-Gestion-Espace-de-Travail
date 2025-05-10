<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EspaceTravailFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Les espaces de travail sont créés via les entités filles :
        // - 10 Bureaux (voir BureauFixtures)
        // - 10 Salles de réunion (voir SalleReunionFixtures)
        // - 10 Espaces de collaboration (voir EspaceCollaborationFixtures)
        // Total : 30 espaces de travail

        $manager->flush();
    }
}
