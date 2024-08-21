<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajouter le champ 'content' pour le contenu du commentaire
            ->add('content', null, [
                'label' => 'Contenu', // Label du champ
                'attr' => ['class' => 'form-control'] // Classe CSS pour le style
            ])

            // Ajouter le champ 'rating' pour la note du commentaire
            ->add('rating', IntegerType::class, [
                'attr' => [
                    'min' => 1, // Valeur minimale autorisée
                    'max' => 5  // Valeur maximale autorisée
                ],
                'required' => true, // Ce champ est obligatoire
                'label' => 'Note' // Label du champ
            ])

            // Ajouter le champ 'user' pour associer un utilisateur au commentaire
            ->add('user', EntityType::class, [
                'class' => User::class, // Classe de l'entité associée
                'choice_label' => 'username', // Propriété de l'entité à afficher dans le champ de sélection
                'label' => 'Utilisateur', // Label du champ
                'required' => true, // Ce champ est obligatoire
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class, // Associe ce formulaire à l'entité Comment
        ]);
    }
}
