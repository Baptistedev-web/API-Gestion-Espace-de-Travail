<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Equipement;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Equipement>
 */
class EquipementFactory extends PersistentProxyObjectFactory
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
        return Equipement::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     * @return array<string, string>
     */
    protected function defaults(): array
    {
        $nom = self::generateRealisticName();

        return [
            'nom' => $nom,
            'description' => self::generateRealisticDescription($nom),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Equipement $equipement): void {})
        ;
    }

    /**
     * Génère un nom réaliste pour un équipement en fonction de la catégorie.
     *
     * @return string
     */
    private static function generateRealisticName(): string
    {
        $faker = self::faker();
        $category = $faker->randomElement(['Bureau', 'Salle de réunion', 'Espace de collaboration']);

        /** @phpstan-ignore-next-line */
        $name = match ($category) {
            'Bureau' => $faker->randomElement(['Chaise', 'Bureau', 'Lampe', 'Écran', 'Clavier', 'Souris']),
            'Salle de réunion' => $faker->randomElement(['Projecteur', 'Table', 'Haut-parleur', 'Tableau blanc', 'Télévision']),
            'Espace de collaboration' => $faker->randomElement(['Canapé', 'Table basse', 'Station de recharge', 'Panneau acoustique', 'Tabouret']),
        };

        return is_string($name) ? $name : 'Équipement générique';
    }

    /**
     * Génère une description réaliste pour un équipement.
     *
     * @param string $nom Le nom de l'équipement.
     * @return string La description générée.
     */
    private static function generateRealisticDescription(string $nom): string
    {
        $faker = self::faker();
        return match ($nom) {
            'Chaise' => 'Une chaise ergonomique idéale pour le bureau, offrant un confort optimal.',
            'Bureau' => 'Un bureau spacieux et moderne, parfait pour travailler efficacement.',
            'Lampe' => 'Une lampe élégante qui éclaire parfaitement votre espace de travail.',
            'Écran' => 'Un écran haute résolution pour une expérience visuelle exceptionnelle.',
            'Clavier' => 'Un clavier mécanique robuste, idéal pour la saisie rapide.',
            'Souris' => 'Une souris sans fil ergonomique pour une navigation fluide.',
            'Projecteur' => 'Un projecteur haute qualité pour vos présentations en salle de réunion.',
            'Table' => 'Une table robuste et élégante, parfaite pour les réunions.',
            'Haut-parleur' => 'Un haut-parleur puissant pour une qualité sonore exceptionnelle.',
            'Tableau blanc' => 'Un tableau blanc magnétique, idéal pour les brainstormings.',
            'Télévision' => 'Une télévision 4K pour des présentations visuelles impressionnantes.',
            'Canapé' => 'Un canapé confortable pour vos espaces de collaboration.',
            'Table basse' => 'Une table basse moderne pour vos espaces de détente.',
            'Station de recharge' => 'Une station de recharge pratique pour vos appareils électroniques.',
            'Panneau acoustique' => 'Un panneau acoustique pour réduire le bruit ambiant.',
            'Tabouret' => 'Un tabouret design et pratique pour vos espaces de travail.',
            default => $faker->sentence(10), // Description générique si le nom ne correspond pas
        };
    }
}

