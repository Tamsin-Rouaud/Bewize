<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EmployeController extends AbstractController
{

    #[IsGranted('IS_AUTHENTICATED')]
    /** Liste tous les employés */
    #[Route('/employes', name: 'app_employes', methods:['GET'])]
    public function index(EmployeRepository $repository): Response
    {
        $employe = $this->getUser();
        if($employe) {
            $this->denyAccessUnlessGranted('ROLE_CHEF_DE_PROJET');
        }
        return $this->render('employe/index.html.twig', [
            'employes' => $repository->findAll(),

        ]);
    }


    /** Affiche les détails d’un employé et permet la modification*/

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/employe/{id}', name: 'app_employe_detail', requirements: ['id'=> '\d+'], methods: ['GET', 'POST'])]
    public function detail(Request $request, Employe $employe, EntityManagerInterface $manager): Response
    {
        if($employe) {
            $this->denyAccessUnlessGranted('ROLE_CHEF_DE_PROJET');
        }

        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $manager->flush();
        $this->addFlash('success', 'Employé mis à jour avec succès.');

        return $this->redirectToRoute('app_employe_detail', ['id' => $employe->getId()]);
    }

    return $this->render('employe/detail.html.twig', [
        'form' => $form->createView(),
        'employe' => $employe,
    ]);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    /** Supprimer un employé */
    #[Route('/employe/{id}/supprimer', name: 'app_employe_supprimer')]
    public function supprimer(Employe $employe, EntityManagerInterface $manager): Response
    {

        if($employe) {
            $this->denyAccessUnlessGranted('ROLE_CHEF_DE_PROJET');
        }
        $manager->remove($employe);
        $manager->flush();

        return $this->redirectToRoute('app_employes');
    }
}
