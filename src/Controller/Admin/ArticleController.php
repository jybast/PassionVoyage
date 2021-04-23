<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/article", name="admin_article_")
 */
class ArticleController extends AbstractController
{
    #[Route('/', name: 'lister')]
    public function lister(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/article/lister.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/activer/{id}', name: 'activer')]
    public function activer(Article $article, ArticleRepository $articleRepository)
    {
        
        // on récupère l'état de Valide : si c'est actif on désactive, sinon on active
        $article->setValide(($article->getValide()) ? false : true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        // envoie la réponse
        return $this->render('admin/article/lister.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
       

    }

    /**
     * @Route("/{id}", name="supprimer", methods={"DELETE"})
     */
    public function supprimer(Request $request, Article $article): Response
    {
        
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_article_lister');
    }
}
