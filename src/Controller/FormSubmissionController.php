<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactMessageType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormSubmissionController extends AbstractController
{
    #[Route('/form/submission', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $data = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($data);
            $entityManager->flush();

            // Send the email
            $mail = (new TemplatedEmail())
                ->to('kolonelaboki78@gmail.com')
                ->from($data->getEmail())
                ->subject('Contact Form Submission')
                ->htmlTemplate('emails/index.html.twig')
                ->context(['data' => $data]);
            
            $mailer->send($mail);

            // Flash message
            $this->addFlash('success', 'Your message has been sent and saved successfully!');

            // Redirect after success
            return $this->redirectToRoute('app_home');
        }

        return $this->render('form_submission/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
