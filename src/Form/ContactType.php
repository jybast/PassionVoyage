<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse mail :',
                'label_attr' => ['class' =>'formulaire-label',],
                'attr' => [
                    'class' => 'formulaire-input'
                ]
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet du message :',
                'label_attr' => ['class' =>'formulaire-label',],
                'attr' => [
                    'class' => 'formulaire-input'
                ]
            ])
            ->add('message', CKeditorType::class, [
                'label' => 'Votre message',
                'label_attr' => ['class' =>'formulaire-label',],
                'config' => ['toolbar' => 'basic']
            ])
            ->add('Envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'formulaire-bouton btn-info'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
