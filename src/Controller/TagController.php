<?php

namespace App\Controller;

use App\Entity\Conseil;
use App\Form\ConseilType;
use App\Repository\ConseilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tag')]
class TagController extends AbstractController
{
    #[Route('/conseil', name: 'conseil_index', methods: ['GET'])]

    public function conseil(ConseilRepository $conseilRepository): Response
    {
        return $this->render('conseil/conseil.html.twig', [
            'conseils' => $conseilRepository->findBy(['type' => 'Conseil']),

        ]);
    }

    #[Route('/fiche', name: 'fiche_index', methods: ['GET'])]

    public function fiche(ConseilRepository $conseilRepository): Response
    {
        return $this->render('conseil/fiche.html.twig', [

            'fiches' => $conseilRepository->findBy(['type' => 'Fiche technique'])
        ]);
    }

    #[Route('/new', name: 'tag_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $conseil = new Conseil();
        $form = $this->createForm(ConseilType::class, $conseil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conseil);
            $entityManager->flush();

            return $this->redirectToRoute('conseil_index');
        }

        return $this->render('conseil/new.html.twig', [
            'conseil' => $conseil,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'tag_show', methods: ['GET'])]
    public function show(Conseil $conseil): Response
    {
        return $this->render('conseil/show.html.twig', [
            'conseil' => $conseil,
        ]);
    }

    #[Route('/{id}/edit', name: 'tag_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Conseil $conseil): Response
    {
        $form = $this->createForm(ConseilType::class, $conseil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('conseil_index');
        }

        return $this->render('conseil/edit.html.twig', [
            'conseil' => $conseil,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'tag_delete', methods: ['DELETE'])]
    public function delete(Request $request, Conseil $conseil): Response
    {
        if ($this->isCsrfTokenValid('delete'.$conseil->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($conseil);
            $entityManager->flush();
        }

        return $this->redirectToRoute('conseil_index');
    }
}
