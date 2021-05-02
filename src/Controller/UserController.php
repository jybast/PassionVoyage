<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
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

#[Route('/user', name:'user_')]
class UserController extends AbstractController
{
  
    #[Route('/{id}', name: 'profil', methods: ['GET'])]
    public function profil(User $user): Response
    {

        // On recherche les données associées au compte
        $articles = $user->getArticles();
        $commentaires = $user->getCommentaires();

        return $this->render('user/profil.html.twig', [
            'user' => $user,
            'articles' => $articles,
            'commentaires' => $commentaires,
        ]);
    }

   
    /**
     * Modifier le profil de l'utilisateur connecté
     * 
     * @Route("/{id}/modifier", name="profil_modifier")
     *
     * @param Request $request
     * @return Response
     */
    public function modifierProfil(Request $request): Response
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
            return $this->redirectToRoute('user_profil');
        }

        return $this->render('user/modifierProfil.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Permet de modifier le mot de passe de l'utilisateur connecté
     *
     * @Route("/{id}/pass/modifier", name="modifierPassword")
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
                    return $this->redirectToRoute('user_profil');

            } else {
                $this->addFlash('error','Les deux mots de passe doivent être identiques.');
            }
        }

        return $this->render('user/modifierPassword.html.twig');
    }

    /**
     * Suppresion du compte par l'utilisateur
     *
     * @Route("/{id}/profil/supprimer", name="supprimer", methods={"DELETE"})
     * @param User $user
     * @param Request $request
     * @return void
     */
    public function supprimerProfil(User $user, Request $request){
        // Vérifier que c'est l'utilisateur propriétaire du compte
        if($user->getId() === $this->getUser()->getID()){
            $ok = true;
            dd($ok);
        }

        

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_accueil');

    }

    /**
     * @Route("/data/download", name="data_download")
     */
    public function dataDownload( ){
        // On définit les options du PDF
        $pdfOptions = new Options();
        // Police par défaut
        $pdfOptions->set('defaultFont', 'Arial');
        // permet le téléchargement SSL
        $pdfOptions->setIsRemoteEnabled(true);
        // On instancie Dompdf
        $dompdf = new Dompdf($pdfOptions);
        // génére le contexte
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($context);

        // On génère le html
        $html = $this->renderView('user/download.html.twig');
        // envoi des data à Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // On génère un nom de fichier
        $fichier = 'user-data-'. $this->getUser()->getId() .'.pdf';

        // On envoie le PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);

        return new Response();
    }
}
