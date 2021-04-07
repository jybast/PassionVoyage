<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur des pages génériques de l'application
 */
class PageController extends AbstractController
{
    /**
     * @Route("/accueil", name="app_accueil")
     *
     * @return Response
     */
    public function accueil(): Response
    {
        
        return $this->render('page/accueil.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    
    /**
     * @Route("/apropos", name="app_apropos")
     *
     * @return Response
     */
    public function apropos(): Response
    {
        return $this->render('page/apropos.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    /**
     * @Route("/contact", name="app_contact")
     *
     * @return Response
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
         // on génère le formulaire
         $form = $this->createForm(ContactType::class);

         // On traite le formulaire
         $contact = $form->handleRequest($request);

          // On traite les données
        if($form->isSubmitted() && $form->isValid()){
            // on crée l'email avec template
            $email = (new TemplatedEmail())
               ->from($contact->get('email')->getData())     // on va chercher dans le formulaire $contact l'email de l'envoi
               ->to('adresse-du-site@domaine.fr ')        // c'est l'adresse du site à contacter
               ->subject('Contact depuis le site - Mon site ' )
               ->htmlTemplate('masques/email-contact.html.twig')     // fichier twig du template
               ->context([                                           // toutes les données dont on a besoin dans le template twig
                   'mail' => $contact->get('email')->getData(),
                   'sujet' => $contact->get('sujet')->getData(),
                   'message' => $contact->get('message')->getData()
               ])
           ;
            
            // envoi du message qui a été créé
            $mailer->send($email);
            
            // on confirme et on redirige
            $this->addFlash('message', 'Votre mail de contact a bien été envoyé');

            // on renvoie sur la route sur la quelle nous sommes 
            return $this-> redirectToRoute('app_home');

        }

        return $this->render('page/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}


