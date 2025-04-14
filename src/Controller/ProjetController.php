<?php

namespace App\Controller;

use App\Security\Voter\ProjetVoter;
use App\Entity\Projet;
use App\Enum\TacheStatus;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class ProjetController extends AbstractController
{
    /*** Afficher la liste des projets  ***/
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/', name: 'app_projets', methods: ['GET'])]
    public function index(ProjetRepository $repository): Response
    {
        
        $employe = $this->getUser();

        if ($this->isGranted('ROLE_CHEF_DE_PROJET')) {
            $projets = $repository->findNonArchives();
        } elseif ($this->isGranted('ROLE_COLLABORATEUR')) {
            $projets = $repository->findByEmployeAndNonArchives($employe);
        } else {
            throw $this->createAccessDeniedException("Accès refusé.");
        }

        return $this->render('projet/index.html.twig', [
            'projets' => $projets,
        ]);
    }

    /*** Afficher un projet en détail avec tableau des statuts ***/
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/projet/{id}', name: 'app_projet_detail', requirements: ['id'=> '\d+'], methods: ['GET'])]
    public function show(int $id, ProjetRepository $repository): Response
    {
        $projet = $repository->find($id);
    
        if (!$projet || $projet->isArchived()) {
            $this->addFlash('warning', 'Ce projet est introuvable ou archivé.');
            return $this->redirectToRoute('app_projets');
        }
            
        $this->denyAccessUnlessGranted(ProjetVoter::VOIR, $projet);

        // Organiser les tâches par statut
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
    
    /*** Créer un nouveau projet ***/
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/ajouter_projet', name: 'app_projet_ajouter', methods:['GET', 'POST'])]
        public function new(?Projet $projet,Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted(ProjetVoter::MODIFIER, $projet);

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

    /*** Modifier un projet ***/
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/projet/{id}/modifier', name: 'app_projet_modifier')]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted(ProjetVoter::MODIFIER, $projet);

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

    /*** Archiver un projet ***/
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/projet/{id}/archiver', name: 'app_projet_archiver')]
    public function archived(Projet $projet, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted(ProjetVoter::MODIFIER, $projet);

        $projet->setIsArchived(true);
        $manager->flush();
    
        $this->addFlash('success', 'Projet archivé avec succès.');
        return $this->redirectToRoute('app_projets'); 
    }
    



}
