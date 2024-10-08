<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Ajouter le champ 'email' de type Email avec des contraintes de validation
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email', // Label du champ
                'attr' => ['placeholder' => 'Entrez votre adresse email'], // Placeholder du champ
                'constraints' => [
                    new Assert\NotBlank(), // Validation : ne pas laisser le champ vide
                    new Assert\Email(),   // Validation : doit être une adresse email valide
                ],
            ])
            
            // Ajouter le champ 'password' avec une répétition pour confirmer le mot de passe
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de Passe', // Label du premier champ
                    'attr' => ['placeholder' => 'Entrez votre mot de passe'], // Placeholder du premier champ
                ],
                'second_options' => [
                    'label' => 'Confirmer le Mot de Passe', // Label du deuxième champ
                    'attr' => ['placeholder' => 'Confirmez votre mot de passe'], // Placeholder du deuxième champ
                ],
                'constraints' => [
                    new Assert\NotBlank(), // Validation : ne pas laisser le champ vide
                    new Assert\Length(['min' => 6]), // Validation : le mot de passe doit contenir au moins 6 caractères
                ],
            ])
        
            // Ajouter le champ 'username' (nom d'utilisateur) de type texte
            ->add('username', TextType::class, [
                'label' => 'Nom d\'Utilisateur', // Label du champ
                'attr' => ['placeholder' => 'Choisissez un nom d\'utilisateur'], // Placeholder du champ
                'constraints' => [
                    new Assert\NotBlank(), // Validation : ne pas laisser le champ vide
                ],
            ])
            
            // Ajouter le champ 'firstName' (prénom) de type texte
            ->add('firstName', TextType::class, [
                'label' => 'Prénom', // Label du champ
                'attr' => ['placeholder' => 'Entrez votre prénom'], // Placeholder du champ
                'constraints' => [
                    new Assert\NotBlank(), // Validation : ne pas laisser le champ vide
                ],
            ])
            
            // Ajouter le champ 'lastName' (nom de famille) de type texte
            ->add('lastName', TextType::class, [
                'label' => 'Nom de Famille', // Label du champ
                'attr' => ['placeholder' => 'Entrez votre nom de famille'], // Placeholder du champ
                'constraints' => [
                    new Assert\NotBlank(), // Validation : ne pas laisser le champ vide
                ],
            ])
            
            // Ajouter le champ 'phone' (numéro de téléphone) de type texte avec validation
            ->add('phone', TextType::class, [
                'label' => 'Numéro de Téléphone', // Label du champ
                'attr' => ['placeholder' => 'Entrez votre numéro de téléphone'], // Placeholder du champ
                'constraints' => [
                    new Assert\NotBlank(), // Validation : ne pas laisser le champ vide
                    new Assert\Regex([
                        'pattern' => '/^\+?[0-9]{7,15}$/', // Validation : le numéro de téléphone doit être valide
                        'message' => 'Veuillez entrer un numéro de téléphone valide.',
                    ]),
                ],
            ])

            // Ajouter le champ 'photo' pour télécharger une photo
            ->add('photo', FileType::class, [
                'label' => false, // Pas de label pour ce champ
                'multiple' => false, // Ne permet pas la sélection de plusieurs fichiers
                'mapped' => false,   // Ce champ n'est pas lié directement à une propriété de l'entité
                'required' => false, // Ce champ n'est pas obligatoire
            ])

            // Ajouter le champ 'roles' (rôles) avec choix multiples
      

            // Ajouter le champ 'terms' (conditions) de type case à cocher
            ->add('terms', CheckboxType::class, [
                'label' => 'J\'accepte les termes et conditions', // Label de la case à cocher
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez accepter les termes et conditions.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // Configurer les options par défaut du formulaire
        $resolver->setDefaults([
            'data_class' => User::class, // Associe ce formulaire à l'entité User
        ]);
    }
}
