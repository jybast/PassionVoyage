<?php

namespace App\Form;

use App\Entity\Motcle;
use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\ImageValidator;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class)
            ->add('legende', TextType::class)
            ->add('sommaire', CKEditorType::class, [
                'config' => [ 'extraPlugins' => 'wordcount', ],
                'plugins' => [
                    'wordcount' => [
                        'path'     => '/bundles/fosckeditor/plugins/wordcount/', 
                        'filename' => 'plugin.js'],
                ]])
            ->add('contenu', CKEditorType::class)
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'allow_extra_fields' => true,
                'multiple' => true,
                
            ])
            ->add('motcle', EntityType::class, [
                'class' => Motcle::class,
                'multiple' => true,
                'allow_extra_fields' => true,
                
            ])
            // Le champ 'images' n'est pas lié à la base de données (mapped => false)
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/jpeg, image/jpg, image/png'
                ],
                'constraints' => [
                    new All([
                       
                        new File([
                            'maxSize' => '4M',
                            'maxSizeMessage' => 'Le fichier ne peut pas dépasser 4Mo'
                        ])
                    ])
                ]
            ])
            //->add('auteur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
