<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/categorie", name="admin_categorie_")
 */
class CategorieController extends AbstractController
{
    #[Route('/', name: 'lister')]
    // Permet de lister les catégories
    public function lister(CategorieRepository $categorieRepository): Response
    {
        return $this->render('admin/categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll()
        ]);
    }

      /**
     * @Route("/ajouter", name="ajouter", methods={"GET","POST"})
     */
    public function ajouter(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('admin_categorie_lister');
        }

        return $this->render('admin/categorie/ajouter.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="afficher", methods={"GET"})
     */
    public function afficher(Categorie $categorie): Response
    {
        return $this->render('admin/categorie/afficher.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="modifier", methods={"GET","POST"})
     */
    public function modifier(Request $request, Categorie $categorie): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_categorie_lister');
        }

        return $this->render('admin/categorie/modifier.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="supprimer", methods={"DELETE"})
     */
    public function supprimer(Request $request, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_categorie_lister');
    }
}
