<?php

namespace App\Form;

use App\Entity\Conseil;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ConseilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class)
            ->add('contenu', CKEditorType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Conseil' => 'Conseil',
                    'Fiche technique' => 'Fiche technique'
                ]
            ])
            ->add('domaine', ChoiceType::class, [
                'choices' => [
                    'Entretien' => 'Entretien',
                    'Mécanique' => 'Mécanique',
                    'Documents' => 'Documents',
                    'Santé' => 'Santé',
                    'Animaux' => 'Animaux'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Conseil::class,
        ]);
    }
}
