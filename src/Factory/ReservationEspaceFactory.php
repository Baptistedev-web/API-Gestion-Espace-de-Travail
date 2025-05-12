<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ReservationEspace;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ReservationEspace>
 */
final class ReservationEspaceFactory extends PersistentProxyObjectFactory
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
        return ReservationEspace::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        $faker = self::faker();
        $dateReservation = $faker->dateTimeBetween('now', '+1 month');
        $heureDebut = $faker->dateTimeBetween(
            $dateReservation->format('Y-m-d') . ' 08:00:00',
            $dateReservation->format('Y-m-d') . ' 16:00:00'
        );
        $heureFin = (clone $heureDebut)->modify('+2 hour');

        $espaceTravailFactories = [
            \App\Factory\BureauFactory::class,
            \App\Factory\SalleReunionFactory::class,
            \App\Factory\EspaceCollaborationFactory::class,
        ];
        /** @var class-string<PersistentProxyObjectFactory<\App\Entity\EspaceTravail>> $factoryClass */
        $factoryClass = $faker->randomElement($espaceTravailFactories);
        $factoryInstance = $factoryClass::new();
        $espaceTravail = $factoryInstance->create();

        return [
            'user'            => UserFactory::randomOrCreate(),
            'espaceTravail'   => $espaceTravail,
            'dateReservation' => $dateReservation,
            'heureDebut'      => $heureDebut,
            'heureFin'        => $heureFin,
            'statut'          => StatutFactory::random(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ReservationEspace $reservationEspace): void {})
        ;
    }
}
