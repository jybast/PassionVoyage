<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/actualite', name: 'admin_actualite_')]
class ActualiteController extends AbstractController
{
    #[Route('/', name: 'lister', methods: ['GET'])]
    public function lister(ActualiteRepository $actualiteRepository): Response
    {
        return $this->render('admin/actualite/lister.html.twig', [
            'actualites' => $actualiteRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: 'ajouter', methods: ['GET', 'POST'])]
    public function ajouter(Request $request): Response
    {
        $actualite = new Actualite();
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actualite);
            $entityManager->flush();

            return $this->redirectToRoute('admin_actualite_lister');
        }

        return $this->render('admin/actualite/ajouter.html.twig', [
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'afficher', methods: ['GET'])]
    public function afficher(Actualite $actualite): Response
    {
        return $this->render('admin/actualite/afficher.html.twig', [
            'actualite' => $actualite,
        ]);
    }

    #[Route('/{id}/modifier', name: 'modifier', methods: ['GET', 'POST'])]
    public function modifier(Request $request, Actualite $actualite): Response
    {
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_actualite_lister');
        }

        return $this->render('admin/actualite/modifier.html.twig', [
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'supprimer', methods: ['DELETE'])]
    public function supprimer(Request $request, Actualite $actualite): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actualite->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actualite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_actualite_lister');
    }
}
