<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\EspaceCollaboration;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<EspaceCollaboration>
 */
final class EspaceCollaborationFactory extends PersistentProxyObjectFactory
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
        return EspaceCollaboration::class;
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
        $espaceNoms = [
            'Atelier Créatif', 'Espace Agile', 'Zone Coworking', 'Espace Cocréation', 'Labo Idées',
            'Espace Design Thinking', 'Hub Innovation', 'Atelier Collaboratif', 'Zone Idéation', 'Espace Partage',
            'Forum Échange', 'Carrefour des Idées', 'Espace Concept', 'Atelier Prototype', 'Zone Workshop'
        ];

        return [
            'capacite'          => self::faker()->numberBetween(10, 50),
            'description'       => 'Espace collaboratif ' . $this->toString(self::faker()->randomElement(['ouvert', 'dynamique', 'multifonctionnel', 'convivial', 'interactif'])) . ' conçu pour ' . $this->toString(self::faker()->randomElement(['favoriser l\'échange d\'idées', 'stimuler la créativité', 'faciliter le travail en équipe', 'encourager l\'innovation', 'permettre des réunions informelles'])),
            'mobilierModulable' => self::faker()->boolean(85),
            'nom'               => $this->toString(self::faker()->randomElement($espaceNoms)) . ' ' . $this->toString(self::faker()->numerify('##')),
            'zoneCafeProche'    => self::faker()->boolean(75),
            'typeAmbiance'      => TypeAmbianceFactory::random(),
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
            // ->afterInstantiate(function(EspaceCollaboration $espaceCollaboration): void {})
        ;
    }
}
