<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\TypeBureau;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<TypeBureau>
 */
final class TypeBureauFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return TypeBureau::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'libelle' => self::faker()->randomElement([
                'Bureau individuel',
                'Bureau partagé',
                'Salle de réunion',
                'Bureau fermé',
                'Espace détente',
                'Espace formation',
                'Phone box',
            ]),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(TypeBureau $typeBureau): void {})
        ;
    }
}
