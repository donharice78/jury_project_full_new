<?php

namespace App\Form;

use App\Entity\Blogs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class BlogsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajouter le champ 'title' pour le titre du blog
            ->add('title', null, [
                'label' => 'Titre', // Label du champ
                'attr' => ['class' => 'form-control'] // Classe CSS pour le style
            ])

            // Ajouter le champ 'content' pour le contenu du blog
            ->add('content', null, [
                'label' => 'Contenu', // Label du champ
                'attr' => ['class' => 'form-control'] // Classe CSS pour le style
            ])

            // Ajouter le champ 'image' pour les images du blog
            ->add('image', FileType::class, [
                'label' => false, // Ne pas afficher de label pour ce champ
                'multiple' => true, // Permet de sélectionner plusieurs fichiers
                'mapped' => false, // Ne pas associer ce champ à une propriété de l'entité
                'required' => false, // Ce champ est optionnel
               ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blogs::class, // Associe ce formulaire à l'entité Blogs
        ]);
    }
}
