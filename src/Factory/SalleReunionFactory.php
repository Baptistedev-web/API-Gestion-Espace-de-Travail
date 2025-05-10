<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\SalleReunion;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<SalleReunion>
 */
final class SalleReunionFactory extends PersistentProxyObjectFactory
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
        return SalleReunion::class;
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
        $salleNoms = [
            'Salle Brainstorm', 'Salle Créativité', 'Salle Innovation', 'Salle Stratégie', 'Salle Projet',
            'Salle Conférence', 'Salle Collaboration', 'Salle Horizon', 'Salle Inspiration', 'Salle Synergie',
            'Salle Partenaires', 'Salle Excellence', 'Salle Perspective', 'Salle Vision', 'Salle Alliance'
        ];

        return [
            'capacite'                  => self::faker()->numberBetween(4, 30),
            'description'               => 'Salle de réunion ' . $this->toString(self::faker()->randomElement(['moderne', 'spacieuse', 'lumineuse', 'intimiste', 'confortable'])) . ' équipée de ' . $this->toString(self::faker()->randomElement(['tableau blanc', 'écran interactif', 'système audio', 'système de projection', 'mur d\'inspiration'])),
            'equipementVisioConference' => self::faker()->boolean(80),
            'nom'                       => $this->toString(self::faker()->randomElement($salleNoms)) . ' ' . $this->toString(self::faker()->numerify('##')),
            'reservationObligatoire'    => self::faker()->boolean(90),
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
            // ->afterInstantiate(function(SalleReunion $salleReunion): void {})
        ;
    }
}
