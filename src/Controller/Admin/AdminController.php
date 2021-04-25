<?php

namespace App\Controller\Admin;

use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
    * @Route("/", name="dashboard")
    */
    public function dashboard(
        UserRepository $userRepository,
        ArticleRepository $articleRepository): Response
    {
        // Cherche tous les utilisateurs
        $users = $userRepository->findAll();
        $articles = $articleRepository->findBy(['valide' => true],['publierAt' => 'desc']);
       
        return $this->render('admin/dashboard.html.twig', [
            'membres' => count($users),
            'articles' => count($articles)
        ]);
    }

    /**
     * @Route("/stats", name="stats")
     */
    public function statistiques(
        CategorieRepository $categorieRepository,
        ArticleRepository $articleRepository)
    {
        // cherche toutes les catégories
        $categories = $categorieRepository->findAll();
        // Décompose la variable en tableaux
        $catNom = [];
        $catCouleur = [];
        $catCount = []; // compte les articles par catégorie

        // boucle
        foreach($categories as $categorie){
            // on remplit les tableaux nomVariable suivi de [] = push
            // catégories est un objet
            $catNom[] = $categorie->getNom();
            $catCouleur[] = $categorie->getCouleur();
            $catCount[] = count($categorie->getArticles());
        }
        // nombre d'articles publiés et validés par date
        $articles = $articleRepository->articlesParDate(true);

        // on décompose
        $dates = []; 
        $articleCount = [];
         // boucle 
         foreach($articles as $article){
            // on remplit les tableaux nomVariable suivi de [] = push
            // $article est un tableau 
            $dates[] = $article['articleDate'];
            $articleCount[] = $article['count'];
        }

        // retourne la vue
        return $this->render('admin/stats.html.twig', [
            'catNom'        => json_encode($catNom),
            'catCouleur'    => json_encode($catCouleur),
            'catCount'      => json_encode($catCount),
            'dates'         => json_encode($dates),
            'articleCount'  => json_encode($articleCount),

        ]);
    }
}
