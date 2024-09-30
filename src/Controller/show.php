<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Course;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/')]

class show extends AbstractController
{
    #[Route('/{lastName}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(string $lastName, EntityManagerInterface $entityManager): Response
    {
        // Fetch the user based on the username from the database
        $user = $entityManager->getRepository(User::class)->findOneBy(['lastName' => $lastName]);

        // If no user is found, throw a 404 exception
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        // Render the view with the fetched user
        return $this->render('admin_user/student_dashboard.html.twig', [
            'user' => $user,
        ]);
    }


}