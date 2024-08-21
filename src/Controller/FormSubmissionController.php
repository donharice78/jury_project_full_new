<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactMessageType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormSubmissionController extends AbstractController
{
    #[Route('/form/submission', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        // Crée une nouvelle instance de ContactMessage
        $contactMessage = new ContactMessage();
        
        // Crée le formulaire basé sur ContactMessage
        $form = $this->createForm(ContactMessageType::class, $contactMessage);

        // Traite les données soumises du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire
            $contactMessage = $form->getData();

            // Crée l'email à envoyer
            $email = (new Email())
                ->from($contactMessage->getEmail())
                ->to('kolonelaboki78@gmail.com')
                ->subject('Soumission de formulaire de contact')
                ->text(
                    sprintf(
                        "Nom: %s\nEmail: %s\nTéléphone: %s\nMessage: %s",
                        $contactMessage->getName(),
                        $contactMessage->getEmail(),
                        $contactMessage->getPhone(),
                        $contactMessage->getMessage()
                    )
                );

            try {
                // Envoie l'email
                $mailer->send($email);
                
                // Affiche un message de succès
                $this->addFlash('success', 'Votre message a été envoyé avec succès !');
            } catch (\Exception $e) {
                // Affiche un message d'erreur en cas de problème
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de l\'email.');
            }

            // Redirige vers la page d'accueil après l'envoi
            return $this->redirectToRoute('app_home');
        }

        // Affiche le formulaire de contact
        return $this->render('form_submission/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
