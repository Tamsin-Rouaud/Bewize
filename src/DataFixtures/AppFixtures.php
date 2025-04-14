<?php

namespace App\DataFixtures;

use App\Factory\EmployeFactory;
use App\Factory\ProjetFactory;
use App\Factory\TacheFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer 5 employés
        EmployeFactory::createMany(5);

        // Créer 3 projets
        ProjetFactory::createMany(3);

        // Créer une tâche liée à un projet et un employé
        TacheFactory::createOne([
            'titre' => 'Créer page projet',
            'description' => 'Créer la vue et le contrôleur',
            'deadline' => new \DateTimeImmutable('+5 days'),
            'employe' => EmployeFactory::random(),
        ]);

        // Créer plusieurs tâches aléatoires
        TacheFactory::createMany(10);
    }
}
