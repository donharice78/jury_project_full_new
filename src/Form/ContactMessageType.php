<?php

namespace App\Form;

use App\Entity\ContactMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajouter le champ 'name' pour le nom d'utilisateur
            ->add('name', TextType::class, [
                'empty_data' => '', // Valeur par défaut du champ
                'label' => 'Nom', // Label du champ
                'attr' => ['class' => 'form-control'] // Classe CSS pour le style
            ])

            // Ajouter le champ 'email' pour l'adresse e-mail
            ->add('email', EmailType::class, [
                'empty_data' => '', // Valeur par défaut du champ
                'label' => 'E-mail', // Label du champ
                'attr' => ['class' => 'form-control'] // Classe CSS pour le style
            ])

            // Ajouter le champ 'phone' pour le numéro de téléphone
            ->add('phone', TelType::class, [
                'empty_data' => '', // Valeur par défaut du champ
                'required' => false, // Ce champ n'est pas obligatoire
                'label' => 'Téléphone', // Label du champ
                'attr' => ['class' => 'form-control'] // Classe CSS pour le style
            ])

            // Ajouter le champ 'message' pour le message du contact
            ->add('message', TextareaType::class, [
                'empty_data' => '', // Valeur par défaut du champ
                'label' => 'Message', // Label du champ
                'attr' => ['class' => 'form-control', 'rows' => 5] // Classe CSS pour le style et spécifie la hauteur en lignes
            ])
           ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configurer les options par défaut du formulaire
        $resolver->setDefaults([
            'data_class' => ContactMessage::class, // Associe ce formulaire à l'entité ContactMessage
        ]);
    }
}
