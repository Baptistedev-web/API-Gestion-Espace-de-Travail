<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Equipement;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * Constructeur de la classe AppFixtures.
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct (UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Charge les données de fixtures dans la base de données.
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {

        // Création d'un user normal
        $user = new User();
        $user->setNom("user");
        $user->setPrenom("user");
        $user->setEmail("user@gmail.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "@Password1234!"));
        $manager->persist($user);

        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setNom("admin");
        $userAdmin->setPrenom("admin");
        $userAdmin->setEmail("admin@icloud.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "@Password1234!"));
        $manager->persist($userAdmin);

        // Création de 10 équipements
        for ($i = 1; $i <= 10; $i++) {
            $equipement = new Equipement();
            $equipement->setNom("Equipement $i");
            $equipement->setDescription("Description de l'équipement $i");
            $manager->persist($equipement);
        }

        $manager->flush();
    }
}
