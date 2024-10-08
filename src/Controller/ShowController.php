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

class ShowController extends AbstractController
{
 
    #[Route('/{username}', name: 'app_admin_user_show')]
    public function show(User $user)
    {
      


        return $this->render('admin_user/student_dashboard.html.twig', [
            'user' => $user,
            
        ]);
    }

}