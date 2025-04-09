<?php

namespace App\Factory;

use App\Entity\Tache;
use App\Enum\TacheStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Tache>
 */
final class TacheFactory extends PersistentProxyObjectFactory
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
        return Tache::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'projet' => ProjetFactory::new(),
            'status' => self::faker()->randomElement(TacheStatus::cases()),
            'titre' => self::faker()->words(2, true),
            'employe' => EmployeFactory::randomOrCreate(),
            'deadline' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('now', '+1 month')),
            'description' =>self::faker()->paragraph(1)

        ];
    }
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Tache $tache): void {})
        ;
    }
}