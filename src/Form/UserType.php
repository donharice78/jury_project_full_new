<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    // Ajouter le champ 'email' de type texte
    $builder
        ->add('email', TextType::class)

        // Ajouter le champ 'username' de type texte
        ->add('username', TextType::class)
        
        // Ajouter le champ 'firstName' (prénom) de type texte
        ->add('firstName', TextType::class)

        ->add('password', PasswordType::class, [
            'label' => 'Password',
            'mapped' => false, // Do not map directly to the entity to avoid accidental rehashing
            'required' => true, // You can set this to false if you don't want it required in updates
        ])
        
        // Ajouter le champ 'lastName' (nom de famille) de type texte
        ->add('lastName', TextType::class)
        
        // Ajouter le champ 'phone' (téléphone) de type texte
        ->add('phone', TextType::class)
        
        // Ajouter le champ 'roles' (rôles) de type choix
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Admin' => 'ROLE_ADMIN',  // Option pour le rôle administrateur
                'User' => 'ROLE_USER',    // Option pour le rôle utilisateur
            ],
            'multiple' => true,  // Permet de sélectionner plusieurs rôles
            'expanded' => true,  // Affiche les choix sous forme de cases à cocher
        ])

        // Ajouter le champ 'photo' de type fichier
        ->add('photo', FileType::class, [
            'label' => false,         // Aucun label pour ce champ
            'multiple' => false,      // Ne permet pas la sélection de plusieurs fichiers
            'mapped' => false,        // Ce champ n'est pas lié directement à une propriété de l'entité
            'required' => false,      // Ce champ n'est pas obligatoire
        ])
    ;
}

public function configureOptions(OptionsResolver $resolver): void
{
    // Configurer les options par défaut du formulaire
    $resolver->setDefaults([
        'data_class' => User::class,  // Associe ce formulaire à l'entité User
    ]);
}

}
