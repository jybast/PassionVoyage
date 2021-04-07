<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_lister", methods={"GET"})
     *
     * @param ArticleRepository $articleRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function lister(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupère tous les articles valides par date de création
        $data= $articleRepository->findBy(['valide' => true],['publierAt' => 'desc']);

        $articles = $paginator->paginate(
            $data, // données à paginer
            $request->query->getInt('page', 1), // n° de page dans URL
            6  // nombre par page
        );
        
        return $this->render('article/lister.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/ajouter", name="article_ajouter", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function ajouter(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_lister');
        }

        return $this->render('article/ajouter.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/details/{slug}", name="article_details", methods={"GET"})
     */
    public function details($slug, ArticleRepository $articleRepository, Request $request, MailerInterface $mailer)
    {
        //récupère l'article à traiter
        $article = $articleRepository->findOneBy(['slug' => $slug]);
        // si article pas trouvé
        if(!$article) {
            throw new NotFoundHttpException('Pas d\'article correspondant à la requête.');
        }

        // création du formulaire
        $form = $this->createForm(ArticleContactType::class);

        // Traitement du formulaire de contact sur l'article
        $contact = $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid()){
            // on crée le mail de contact
            $email = (new TemplatedEmail())
                ->from($contact->get('email')->getData())
                ->to($article->getAuteur()->getEmail())
                ->subject('Contact au sujet de votre article' . $article->getTitre())
                ->htmlTemplate('article/contact_article.html.twig')
                ->context([
                    'article' => $article,
                    'mail' => $contact->get('email')->getData(),
                    'message' => $contact->get('message')->getData()
                ]);
            // on envoie le mail
            $mailer->send($email);

            // On confirme et on redirige
            $this->addFlash('message', 'Votre mail a été envoyé.');
            return $this->redirectToRoute('article_details', ['slug' => $article->getSlug()]);

        }

        /***  Partie gestion des commentaires *****/
        // on crée un commentaire vierge - instancie l'objet
        $commentaire = new Commentaire;

        // on génère le formulaire
        $commentForm = $this->createForm(CommentaireType::class);

        $commentForm->handleRequest($request);

        // traitement du formulaire commentForm




        return $this->render('article/details.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'commentForm' => $commentForm->createView()   // génère le html du formulaire
        ]);

    }

    /**
     * @Route("/{id}", name="article_afficher", methods={"GET"})
     *
     * @param Article $article
     * @return Response
     */
    public function afficher(Article $article): Response
    {
        return $this->render('article/afficher.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="article_modifier", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function modifier(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_lister');
        }

        return $this->render('article/modifier.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="article_supprimer", methods={"DELETE"})
     */
    public function supprimer(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_lister');
    }
}
