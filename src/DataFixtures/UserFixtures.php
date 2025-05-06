<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * Constructeur de la classe UserFixtures.
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


        // Générer 10 utilisateurs supplementaires avec informations réalistes
        UserFactory::createMany(10);

        $manager->flush();
    }
}