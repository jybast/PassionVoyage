<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfilUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Votre adresse mail',
            'label_attr' => [
                'class' => 'formulaire-label'
            ],
            'attr' =>[
                'class' => 'formulaire-input'
            ]
        ])
        //->add('roles')

        ->add('pseudo', TextType::class, [
            'label' => 'Votre pseudo',
            'label_attr' => [
                'class' => 'formulaire-label'
            ],
            'attr' =>[
                'class' => 'formulaire-input'
            ]
        ])
        ->add('nom', TextType::class, [
            'label' => 'Votre nom',
            'label_attr' => [
                'class' => 'formulaire-label'
            ],
            'attr' =>[
                'class' => 'formulaire-input'
            ]
        ])
        ->add('prenom', TextType::class, [
            'label' => 'Votre prénom',
            'label_attr' => [
                'class' => 'formulaire-label'
            ],
            'attr' =>[
                'class' => 'formulaire-input'
            ]
        ])
        ->add('adresse', TextType::class, [
            'label' => 'Votre adresse (n° de voie, rue)',
            'label_attr' => [
                'class' => 'formulaire-label'
            ],
            'attr' =>[
                'class' => 'formulaire-input'
            ]
        ])
        ->add('ville', TextType::class, [
            'label' => 'Votre ville',
            'label_attr' => [
                'class' => 'formulaire-label'
            ],
            'attr' =>[
                'class' => 'formulaire-input'
            ]
        ])
        ->add('cp', TextType::class, [
            'label' => 'Votre code postal',
            'label_attr' => [
                'class' => 'formulaire-label'
            ],
            'attr' =>[
                'class' => 'formulaire-input'
            ]
        ])
        ->add('telephone', TextType::class, [
            'label' => 'Votre téléphone',
            'label_attr' => [
                'class' => 'formulaire-label'
            ],
            'attr' =>[
                'class' => 'formulaire-input'
            ]
        ])
        //->add('isVerified')
       ->add('Valider', SubmitType::class, [
           'label' => 'Valider les modifications',
           'attr' => [
               'class' => 'formulaire-bouton btn-info'
           ]
       ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
