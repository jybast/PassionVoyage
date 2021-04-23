<?php

namespace App\Controller;

use DateTime;
use App\Entity\Media;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Form\ArticleContactType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        // création d'une instance pour l'article
        $article = new Article();
        
        // génère l'objet formulaire de l'article
        $form = $this->createForm(ArticleType::class, $article);
        // Traitement des données de la request
        $form->handleRequest($request);
        // récupère l'instance de l'utilisateur connecté
        $article->setAuteur($this->getUser());

        // Traitement des données envoyées dans le formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // récupère l'instance de l'utilisateur connecté
            $article->setAuteur($this->getUser());
            // Mise en attente de modération valide = false
            $article->setValide(false);

            // on récupère les images transmises
            $images = $form->get('images')->getData();
            // on boucle sur les images
            foreach($images as $image){
                // on génère un nom de fichier image unique
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                // on copie l'image physique dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'), // c'est la destination
                    $fichier                                 // le fichier à copier
                );
                // on stocke le nom de l'image dans la base de données
                $img = new Media();
                $img->setNom($fichier);
                //$img->setType($image->guessExtension());
                // lie les images à l'article
                $article->addMedium($img);
            }


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
     * @Route("/{slug}", name="article_afficher", methods={"GET", "POST"})
     */
    public function afficher($slug, ArticleRepository $articleRepository, Request $request, MailerInterface $mailer)
    {
        //récupère l'article à traiter
        $article = $articleRepository->findOneBy(['slug' => $slug]);
        // si article pas trouvé
        if(!$article) {
            throw new NotFoundHttpException('Pas d\'article correspondant à la requête.');
        }
        // création du formulaire de contact
        $form = $this->createForm(ArticleContactType::class);
        // Traitement du formulaire de contact sur l'article
        $contact = $form->handleRequest($request);
        //Traitement des données du formulaire
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
            return $this->redirectToRoute('article_afficher', ['slug' => $article->getSlug()]);
        }

        /***  Partie gestion des commentaires *****/
        // on crée un commentaire vierge - instancie l'objet
        $commentaire = new Commentaire;
        // on génère le formulaire du commentaire
        $commentForm = $this->createForm(CommentaireType::class);
        $commentForm->handleRequest($request);

        // traitement du formulaire commentForm
        if($commentForm->isSubmitted() && $commentForm->isValid()){
         // Récupère l'auteur qui doit être un utilisateur connecté
            $commentaire->setAuteur($this->getUser());
            // récupère la date de publication
            $commentaire->setPublierAt( new DateTime());
            // je relie le commentaire à l'article
            $commentaire->setArticle($article);
            // On récupère le contenu du champ parentid
            $parentid = $commentForm->get("parentId")->getData();
            // On récupère le contenu du commentaire
            $contenu = $commentForm->get("contenu")->getData();
            $commentaire->setContenu($contenu);
            // on récupère le flag du rgpd
            $rgpd = $commentForm->get("rgpd")->getData();
            $commentaire->setRgpd($rgpd);

            // On va chercher le commentaire correspondant
            $em = $this->getDoctrine()->getManager();
            if($parentid != null){
                $parent = $em->getRepository(Commentaire::class)->find($parentid);
            }

             // On définit le parent
            $commentaire->setParent($parent ?? null);

            $em->persist($commentaire);
            $em->flush();

            $this->addFlash('message', 'Votre commentaire a bien été envoyé, il sera en attente de modération');
            // retour sur la page
            return $this->redirectToRoute('article_afficher', ['slug' => $article->getSlug()]);
            
        }
  

        return $this->render('article/afficher.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'commentForm' => $commentForm->createView(),   // génère le html du formulaire
            
        ]);

    }

    /**
     * @Route("/afficher/{id}", name="article_details", methods={"GET"})
     *
     * @param Article $article
     * @return Response
     */
    public function afficherArticle(Article $article): Response
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
            // on récupère les images transmises
            $images = $form->get('images')->getData();
            // on boucle sur les images
            foreach($images as $image){
                // on génère un nom de fichier image unique
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                // on copie l'image physique dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'), // c'est la destination
                    $fichier                                 // le fichier à copier
                );
                // on stocke le nom de l'image dans la base de données
                $img = new Media();
                $img->setNom($fichier);
                //$img->setType($image->guessExtension());
                // lie les images à l'article
                $article->addMedium($img);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_lister');
        }

        return $this->render('article/modifier.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }


    ///** 
    // * @Route("/{id}", name="article_supprimer", methods={"DELETE"})
    //*/
    //public function supprimer(Request $request, Article $article): Response
    //{
    //    if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
    //        $entityManager = $this->getDoctrine()->getManager();
    //        $entityManager->remove($article);
    //        $entityManager->flush();
    //    }
    //    return $this->redirectToRoute('article_lister');
    //}
     
    /**
     * 
     * @Route("/supprimer/image/{id}", name="article_supprimer_image", methods={"DELETE", "GET"} )
     *
     * @param Media $image
     * @param Request $request
     * @return void
     */
   public function supprimerImage(Media $image, Request $request){
        $data = json_decode($request->getContent(), true);

        // on vérifie si le token est valide
        if( $this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            // on récuprère le nom de l'image
            $nom = $image->getNom();
            // on supprime le fichier de son répertoire
            unlink($this->getParameter('images_directory').'/'.$nom);
            // on supprime l'enregistrement dans la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // on retourne la réponse en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
     }
}
