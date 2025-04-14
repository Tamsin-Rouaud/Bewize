<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Form\TacheType;
use App\Security\Voter\TacheVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TacheController extends AbstractController
{
    /*** Afficher les tâches d’un projet ***/
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/projet/{id}/taches', name: 'app_taches_projet', methods:['GET'])]
    public function index(Projet $projet): Response
    {
            
        return $this->render('projet/index.html.twig', [
            'projet' => '$projet',
            'taches' => $projet->getTaches(),
        ]);
    }

    /*** Ajouter un nouveau projet ***/
    #[IsGranted('ROLE_CHEF_DE_PROJET')]
    #[Route('/projet/{id}/tache/ajouter', name: 'app_tache_ajouter', methods: ['GET', 'POST'])]
    public function new(Request $request, Projet $projet, EntityManagerInterface $manager): Response
    {

        $tache = new Tache();
        $tache->setProjet($projet); 

        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($tache);
            $manager->flush();

            return $this->redirectToRoute('app_projet_detail', [
                'id' => $projet->getId()
            ]);
        }

        return $this->render('tache/new.html.twig', [
            'form' => $form->createView(),
            'projet' => $projet, 
            'tache' => $tache,
        ]);
    }

    /*** Permettre la modification d'une tâche ***/
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/tache/{id}', name: 'app_tache_modifier', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $manager): Response
    {
        // Vérifie d'abord s'il a le droit de voir cette tâche
        $this->denyAccessUnlessGranted(TacheVoter::VOIR, $tache);

        $isChef = $this->isGranted('ROLE_CHEF_DE_PROJET');
        $canEditStatus = $this->isGranted(TacheVoter::MODIFIER_STATUT, $tache);

        // Formulaire selon le rôle
        if ($isChef) {
            $form = $this->createForm(TacheType::class, $tache);
        } elseif ($canEditStatus) {
            $form = $this->createForm(TacheType::class, $tache, [
                'is_collaborateur' => true,
            ]);
        } else {
            $form = $this->createForm(TacheType::class, $tache, [
                'disabled' => true,
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && ($isChef || $canEditStatus)) {
            $manager->flush();

            return $this->redirectToRoute('app_projet_detail', [
                'id' => $tache->getProjet()->getId()
            ]);
        }

        return $this->render('tache/new.html.twig', [
            'form' => $form->createView(),
            'projet' => $tache->getProjet(),
            'tache' => $tache,
        ]);
    }

    /*** Supprimer une tâche ***/
    #[IsGranted('ROLE_CHEF_DE_PROJET')]
    #[Route('/tache/{id}/supprimer', name: 'app_tache_supprimer')]
    public function delete(Tache $tache, EntityManagerInterface $manager): Response
    {
    
        if (!$this->isGranted('ROLE_CHEF_DE_PROJET')) {
            throw $this->createAccessDeniedException("Seul un chef de projet peut supprimer une tâche.");
        }

        $projetId = $tache->getProjet()->getId();

        $manager->remove($tache);
        $manager->flush();

        return $this->redirectToRoute('app_projet_detail', [
            'id' => $projetId
        ]);
    }


}
