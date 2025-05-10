<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\TypeAmbiance;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<TypeAmbiance>
 */
final class TypeAmbianceFactory extends PersistentProxyObjectFactory
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
        return TypeAmbiance::class;
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
                'Silencieuse',
                'Collaborative',
                'Créative',
                'Détente',
                'Dynamique',
                'Nomade',
                'Nature',
            ]),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(TypeAmbiance $typeAmbiance): void {})
        ;
    }
}
