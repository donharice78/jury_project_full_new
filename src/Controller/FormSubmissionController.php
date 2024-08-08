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
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $contactMessage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get form data
            $contactMessage = $form->getData();

            // Send email
            $email = (new Email())
                ->from($contactMessage->getEmail())
                ->to('kolonelaboki78@gmail.com')
                ->subject('Contact Form Submission')
                ->text(
                    sprintf(
                        "Name: %s\nEmail: %s\nPhone: %s\nMessage: %s",
                        $contactMessage->getName(),
                        $contactMessage->getEmail(),
                        $contactMessage->getPhone(),
                        $contactMessage->getMessage()
                    )
                );

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Your message has been sent!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while sending the email.');
            }

            return $this->redirectToRoute('app_form_submission');
        }

        return $this->render('form_submission/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
