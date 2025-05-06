<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array
    {
        $plainPassword = self::generateValidPassword();
        $hashedPassword = $this->passwordHasher->hashPassword(new User(), $plainPassword);

        return [
            'email'    => self::faker('fr_FR')->unique()->safeEmail(),
            'nom'      => self::faker('fr_FR')->lastName(),
            'prenom'   => self::faker('fr_FR')->firstName(),
            'password' => $hashedPassword,
            'roles'    => ['ROLE_USER'],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }
    /**
     * Génère un mot de passe valide respectant les contraintes définies.
     */
    private static function generateValidPassword(): string
    {
        $faker = self::faker();

        // Générer un mot de passe conforme
        $password = $faker->regexify('(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}');
        return $password;
    }
}
