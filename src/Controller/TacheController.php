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

#[IsGranted('IS_AUTHENTICATED')]
/** Affiche les tâches d’un projet */
#[Route('/projet/{id}/taches', name: 'app_taches_projet', methods:['GET'])]
public function index(Projet $projet): Response
{
    
   
    return $this->render('projet/index.html.twig', [
        'projet' => '$projet',
        'taches' => $projet->getTaches(),
    ]);
}

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
        'projet' => $projet, // ✅ au lieu de 'projets'
        'tache' => $tache, // nécessaire pour afficher `{{ tache.titre }}` dans le template
    ]);
}

#[IsGranted('IS_AUTHENTICATED')]
#[Route('/tache/{id}', name: 'app_tache_modifier', methods: ['GET', 'POST'])]
public function edit(Request $request, Tache $tache, EntityManagerInterface $manager): Response
{
    // Vérifie d'abord s'il a le droit de voir cette tâche
    $this->denyAccessUnlessGranted(TacheVoter::VOIR, $tache);

    $user = $this->getUser();

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






    /** Supprime une tâche */
    #[IsGranted('ROLE_CHEF_DE_PROJET')]
    #[Route('/tache/{id}/supprimer', name: 'app_tache_supprimer')]
    public function delete(Tache $tache, EntityManagerInterface $manager): Response
    {
        $employe = $this->getUser();

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
