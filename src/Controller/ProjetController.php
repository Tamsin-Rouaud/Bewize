<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Enum\TacheStatus;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjetController extends AbstractController
{
    /** Affiche tous les projets non archivés */
    #[Route('/', name: 'app_projets', methods:['GET'])]
    public function index(ProjetRepository $repository): Response
    {
        $projets = $repository->findNonArchives();


        return $this->render('projet/index.html.twig', [
            
            'projets' => $projets,
        ]);
    }

    /** Affiche un projet en détail avec tableau des statuts */
#[Route('/projet/{id}', name: 'app_projet_detail', requirements: ['id'=> '\d+'], methods: ['GET'])]
public function show(int $id, ProjetRepository $repository): Response
{
    $projet = $repository->find($id);

    if (!$projet || $projet->isArchived()) {
        $this->addFlash('warning', 'Ce projet est introuvable ou archivé.');
        return $this->redirectToRoute('app_projets');
    }

    // Crée une structure pour organiser les tâches par statut
    $statusList = [];
    foreach (TacheStatus::cases() as $status) {
        $statusList[$status->value] = [];
    }

    foreach ($projet->getTaches() as $tache) {
        $key = $tache->getStatus()->value;
        $statusList[$key][] = $tache;
    }

    return $this->render('projet/detail.html.twig', [
        'projet' => $projet,
        'statusList' => $statusList,
    ]);
}



    /** Crée un nouveau projet */
    #[Route('/ajouter_projet', name: 'app_projet_ajouter', methods:['GET', 'POST'])]
        public function new(?Projet $projet,Request $request, EntityManagerInterface $manager): Response
    {
        $projet ??= new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($projet);
            $manager->flush();

            return $this->redirectToRoute('app_projet_detail', ['id' => $projet->getId()]);
        }

        return $this->render('projet/new.html.twig', [
            'form' => $form,
        ]);
    }

    /** Modifie un projet */
    #[Route('/projet/{id}/modifier', name: 'app_projet_modifier')]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($projet);
            $manager->flush();

            return $this->redirectToRoute('app_projet_detail', ['id' => $projet->getId()]);
        }

        return $this->render('projet/new.html.twig', [
            'form' => $form,
            'projet' => $projet
        ]);
    }

    /**
     * Archiver un projet
     */
    #[Route('/projet/{id}/archiver', name: 'app_projet_archiver')]
    public function archived(Projet $projet, EntityManagerInterface $manager): Response
    {
        $projet->setIsArchived(true);
        $manager->flush();
    
        $this->addFlash('success', 'Projet archivé avec succès.');
        return $this->redirectToRoute('app_projets'); // ou ta page d'accueil
    }
    

//     #[Route('/projet/{id}', name: 'app_projet_detail')]
// public function createStatusBoard(int $id, ProjetRepository $repository): Response
// {
//     $projet = $repository->find($id);

//     if (!$projet) {
//         throw $this->createNotFoundException('Projet non trouvé');
//     }

//     // Crée une structure pour organiser les tâches par statut
//     $statusList = [];
//     foreach (TacheStatus::cases() as $status) {
//         $statusList[$status->value] = [];
//     }

//     // Classe les tâches selon leur statut
//     foreach ($projet->getTaches() as $tache) {
//         $key = $tache->getStatus()->value;
//         $statusList[$key][] = $tache;
//     }

//     return $this->render('projet/detail.html.twig', [
//         'projet' => $projet,
//         'statusList' => $statusList,
//     ]);
// }

}
