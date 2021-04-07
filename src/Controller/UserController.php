<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ProfilUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'listerUser', methods: ['GET'])]
    public function listerUser(UserRepository $userRepository): Response
    {
        return $this->render('user/listerUser.html.twig', [
            'users' => $userRepository->findBy(['isVerified' => true],['nom' => 'desc']),
        ]);
    }

    #[Route('/ajouter', name: 'ajouterUser', methods: ['GET', 'POST'])]
    public function ajouterUser(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('listerUser');
        }

        return $this->render('user/ajouterUser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'profilUser', methods: ['GET'])]
    public function profilUser(User $user): Response
    {
        // On recherche les données associées au compte
        $articles = $user->getArticles();
        $commentaires = $user->getCommentaires();

      
      
      

        return $this->render('user/profilUser.html.twig', [
            'user' => $user,
            'articles' => $articles,
            'commentaires' => $commentaires,
        ]);
    }

   
    /**
     * Modifier le profil de l'utilisateur connecté
     * 
     * @Route("/user/profil/modifier", name="user_profil_modifier")
     *
     * @param Request $request
     * @return Response
     */
    public function editProfile(Request $request): Response
    {
        // récupère l'utilisateur connecté
        $user = $this->getUser();
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
            $this->addFlash('message', 'votre profil a été mis à jour.');
            // redirection vers la page profil
            return $this->redirectToRoute('profilUser');
        }

        return $this->render('user/modifierProfil.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/{id}/modifier', name: 'modifierProfil', methods: ['GET', 'POST'])]
    public function modifierProfil(Request $request, User $user): Response
    {
        $form = $this->createForm(ProfilUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('listerUser');
        }

        return $this->render('user/modifierProfil.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/supprimer/{id}', name: 'supprimerUser', methods: ['DELETE'])]
    public function supprimerUser(Request $request, User $user): Response
    {

        dd($user);
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_accueil');
    }

 
    /**
     * Permet de modifier le mot de passe de l'utilisateur connecté
     *
     * @Route("/user/pass/modifier", name="modifierPassword")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function modifierPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // vérifier que method en mode POST
        if($request->isMethod('POST')){
            $em=$this->getDoctrine()->getManager();
            // récupère le user depuis la requête
            $user= $this->getUser();

            // vérifier concordance des mots de passe
            if($request->request->get('pass') == $request->request->get('pass2')){
                // stocker et encoder le mot de passe
                    $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('pass')));
                    // envoi à la base de données
                    $em->flush();

                    // message de confirmation
                    $this->addFlash('message', 'Votre mot de passe a été mis à jour.');

                    // redirection vers la page profil utilisateur
                    return $this->redirectToRoute('profilUser');

            } else {
                $this->addFlash('error','Les deux mots de passe doivent être identiques.');
            }
        }

        return $this->render('user/modifierPassword.html.twig');
    }
}
