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
        // Crée 5 employés
        EmployeFactory::createMany(5);

        // Crée 3 projets
        ProjetFactory::createMany(3);

        // Crée une tâche liée à un projet et un employé
        TacheFactory::createOne([
            'titre' => 'Créer page projet',
            'description' => 'Créer la vue et le contrôleur',
            'deadline' => new \DateTimeImmutable('+5 days'),
            'employe' => EmployeFactory::random(),
        ]);

        // Crée plusieurs tâches aléatoires
        TacheFactory::createMany(10);
    }
}
