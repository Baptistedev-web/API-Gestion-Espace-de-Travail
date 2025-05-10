<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\EspaceTravail;
use App\Repository\EspaceTravailRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<EspaceTravail>
 */
final class EspaceTravailFactory extends PersistentProxyObjectFactory
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
        return EspaceTravail::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        $espacesTravailNoms = [
            'Espace Créatif', 'Hub Central', 'Zone Innovation', 'Espace Dynamique', 'Centre Productif',
            'Nexus Travail', 'Espace Synergie', 'Laboratoire d\'Idées', 'Centre Collaboratif', 'Espace Vision',
            'Hub Stratégie', 'Zone Concept', 'Atelier Digital', 'Studio Projet', 'Quartier des Talents',
            'Carrefour Professionnel', 'Centre Expertise', 'Espace Excellence', 'Pôle Performance', 'Sphère Développement'
        ];

        return [
            'capacite'    => self::faker()->numberBetween(5, 100),
            'description' => self::faker()->paragraph(3),
            'nom'         => $this->toString(self::faker()->randomElement($espacesTravailNoms)) . ' ' . $this->toString(self::faker()->numerify('##')),
        ];
    }

    private function toString(mixed $value): string
    {
        return is_scalar($value) || $value === null ? (string) $value : '';
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(EspaceTravail $espaceTravail): void {})
        ;
    }
}
