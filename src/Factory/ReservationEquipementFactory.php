<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ReservationEquipement;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ReservationEquipement>
 */
final class ReservationEquipementFactory extends PersistentProxyObjectFactory
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
        return ReservationEquipement::class;
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
        $faker = self::faker();
        $dateReservation = $faker->dateTimeBetween('now', '+1 month');
        $heureDebut = $faker->dateTimeBetween(
            $dateReservation->format('Y-m-d') . ' 08:00:00',
            $dateReservation->format('Y-m-d') . ' 16:00:00'
        );
        $heureFin = (clone $heureDebut)->modify('+2 hour');

        return [
            'user' => UserFactory::randomOrCreate(),
            'equipement' => EquipementFactory::randomOrCreate(),
            'dateReservation' => $dateReservation,
            'heureDebut' => $heureDebut,
            'heureFin' => $heureFin,
            'statut' => StatutFactory::random(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ReservationEquipement $reservationEquipement): void {})
        ;
    }
}
