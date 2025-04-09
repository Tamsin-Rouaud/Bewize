<?php

namespace App\Controller;


use App\Entity\Projet;
use App\Entity\Tache;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TacheController extends AbstractController
{

/** Affiche les tâches d’un projet */
#[Route('/projet/{id}/taches', name: 'app_taches_projet', methods:['GET'])]
public function index(Projet $projet): Response
{
   
    return $this->render('projet/index.html.twig', [
        'projet' => '$projet',
        'taches' => $projet->getTaches(),
    ]);
}


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

#[Route('/tache/{id}', name: 'app_tache_modifier', methods: ['GET', 'POST'])]
public function edit(Request $request, Tache $tache, EntityManagerInterface $manager): Response
{
    $form = $this->createForm(TacheType::class, $tache);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $manager->flush();

        return $this->redirectToRoute('app_projet_detail', [
            'id' => $tache->getProjet()->getId()
        ]);
    }

    return $this->render('tache/new.html.twig', [
        'form' => $form->createView(),
        'projet' => $tache->getProjet(), // ✅ cohérence avec le template
        'tache' => $tache,
    ]);
}


    /** Supprime une tâche */
    #[Route('/tache/{id}/supprimer', name: 'app_tache_supprimer')]
    public function delete(Tache $tache, EntityManagerInterface $manager): Response
    {
        $projetId = $tache->getProjet()->getId();
    
        $manager->remove($tache);
        $manager->flush();
    
        return $this->redirectToRoute('app_projet_detail', [
            'id' => $projetId
        ]);
    }
    



}
