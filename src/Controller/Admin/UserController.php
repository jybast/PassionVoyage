<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ProfilUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/user', name:'admin_user_')]
class UserController extends AbstractController
{
    #[Route('/', name: '', methods: ['GET'])]
    /**
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à consulter cette page !")
     * @Route("/", name="listerUser", methods={"GET"})
     *
     * @param UserRepository $userRepository
     * @return Response
     */
    public function listerUser(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/listerUser.html.twig', [
            'users' => $userRepository->findBy(['isVerified' => true],['nom' => 'desc']),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à consulter cette page !")
     * @Route("/ajouter", name="ajouterUser", methods={"GET", "POST"})
     * 
     * @param Request $request
     * @return Response
     */
    public function ajouterUser(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_listerUser');
        }

        return $this->render('admin/user/ajouterUser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à consulter cette page !")
     * @Route("/supprimer/{id}", name="supprimerUser", methods={"DELETE"})
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function supprimerUser(Request $request, User $user): Response
    {


        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_accueil');
    }

    #[Route('/afficher/{id}', name: 'afficherUser', methods: [''])]
    /**
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à consulter cette page !")
     * @Route("/afficher/{id}", name="afficherUser", methods={"GET"})
     *
     * @param User $user
     * @return Response
     */
    public function afficherUser(User $user): Response
    {
        dd($user);
        // On recherche les données associées au compte
        $articles = $user->getArticles();
        $commentaires = $user->getCommentaires();

        return $this->render('admin/user/afficherUser.html.twig', [
            'user' => $user,
            'articles' => $articles,
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/{id}', name: 'profilUser', methods: ['GET'])]
    public function profilUser(User $user): Response
    {

        // On recherche les données associées au compte
        $articles = $user->getArticles();
        $commentaires = $user->getCommentaires();

        return $this->render('admin/user/profilUser.html.twig', [
            'user' => $user,
            'articles' => $articles,
            'commentaires' => $commentaires,
        ]);
    }

   
    /**
     * Modifier le profil d'un utilisateur 
     * 
     * @Route("/{id}/editer", name="profil_editer")
     *
     * @param Request $request
     * @return Response
     */
    public function editProfile(Request $request, User $user): Response
    {
        // on génére un formulaire on lui passant l'utilisateur connecté
        $form= $this->createForm(ProfilUserType::class, $user);
        // on traite les données de Request
        $form->handleRequest($request);
        // on traite le formulaire
        if($form->isSubmitted() && $form->isValid()){
 
            // on traite les données
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // message de confirmation
            $this->addFlash('message', 'Le profil a été mis à jour.');
            // redirection vers la page profil
            return $this->redirectToRoute('profilUser');
        }

        return $this->render('admin/user/modifierProfil.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    
}
