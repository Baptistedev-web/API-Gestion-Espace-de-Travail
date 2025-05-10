<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Bureau;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Bureau>
 */
final class BureauFactory extends PersistentProxyObjectFactory
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
        return Bureau::class;
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
        $bureauNoms = [
            'Bureau Executive', 'Bureau Confort', 'Bureau Moderne', 'Bureau Panorama', 'Bureau Premium',
            'Bureau Elite', 'Bureau Horizon', 'Bureau Prestige', 'Bureau Influence', 'Bureau Zenith',
            'Bureau Succès', 'Bureau Performance', 'Bureau Optimal', 'Bureau Vision', 'Bureau Stratégie'
        ];

        $capacite = self::faker()->numberBetween(1, 8);

        return [
            'capacite'              => $capacite,
            'description'           => sprintf(
                'Bureau %s avec %s',
                $this->toString(self::faker()->randomElement(['privé', 'partagé', 'open space', 'silencieux', 'créatif'])),
                $this->toString(self::faker()->randomElement(['vue sur la ville', 'éclairage naturel', 'mobilier ergonomique', 'espace de rangement optimisé', 'décoration moderne']))
            ),
            'disponibleEnPermanent' => self::faker()->boolean(70),
            'nom'                   => sprintf(
                '%s %s',
                $this->toString(self::faker()->randomElement($bureauNoms)),
                $this->toString(self::faker()->numerify('##'))
            ),
            'nombrePoste'           => $capacite,
            'typeBureau'            => TypeBureauFactory::random(),
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
            // ->afterInstantiate(function(Bureau $bureau): void {})
        ;
    }
}
