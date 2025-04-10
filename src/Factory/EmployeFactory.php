<?php

namespace App\Factory;

use App\Entity\Employe;
use App\Enum\EmployeStatus;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Employe>
 */
final class EmployeFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Employe::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'date_entree' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'email' => self::faker()->unique()->email(),
            'nom' => self::faker()->text(10),
            'prenom' => self::faker()->text(6),
            'status' => self::faker()->randomElement(EmployeStatus::cases()),
            'roles' => ['ROLE_COLLABORATEUR', 'ROLE_CHEF_DE_PROJET', 'ROLE_ADMIN'],
            'password'=>$this->hasher->hashPassword(new Employe(),'password'),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Employe $employe): void {})
        ;
    }
}
