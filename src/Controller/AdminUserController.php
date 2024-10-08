<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Course;
use App\Form\UserType;
use App\Form\AttributeCourseType;
use App\Repository\UserRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/back')]
class AdminUserController extends AbstractController
{
    #[Route('/main', name: 'app_admin_dashboard', methods: ['GET'])]
    public function index(UserRepository $userRepository, CourseRepository $courseRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You need to be logged in to access this page.');
        }

        // Get all users, filter non-admin and admin users
        $users = $userRepository->findAll();
        $nonAdminUsers = array_filter($users, fn($user) => in_array('ROLE_USER', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles()));
        $adminUsers = array_filter($users, fn($user) => in_array('ROLE_ADMIN', $user->getRoles()));

        $totalNonAdminUsers = count($nonAdminUsers);
        $totalAdminUsers = count($adminUsers);
        $courses = $courseRepository->findAll();
        $totalCourses = count($courses);

        return $this->render('admin_user/admin_dashboard.html.twig', [
            'total_etudiants' => $totalNonAdminUsers,
            'total_admin' => $totalAdminUsers,
            'total_courses' => $totalCourses,
            'courses' => $courses,
        ]);
    }

    #[Route('/non_admin_users', name: 'app_non_admin_user_index', methods: ['GET'])]
    public function nonAdminUser(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $nonAdminUsers = array_filter($users, fn($user) => in_array('ROLE_USER', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles()));

        return $this->render('admin_user/non_admin_index.html.twig', [
            'users' => $nonAdminUsers,
        ]);
    }



    #[Route('/admin_list', name: 'app_all_admin_user_index', methods: ['GET'])]
    public function adminUser(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $adminUsers = array_filter($users, fn($admin) => in_array('ROLE_ADMIN', $admin->getRoles()));

        return $this->render('admin_user/admin_index.html.twig', [
            'users' => $adminUsers,
        ]);
    }

    #[Route('/courses', name: 'app_admin_user_course', methods: ['GET'])]
    public function courses(CourseRepository $courseRepository): Response
    {
        $courses = $courseRepository->findAll();

        return $this->render('course/index.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload if present
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $newFileName = uniqid() . '.' . $photo->guessExtension();
                try {
                    $photo->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newFileName);
                    $user->setPhoto('/uploads/' . $newFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_admin_user_new');
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin_user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $newFileName = uniqid() . '.' . $photo->guessExtension();
                try {
                    $photo->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newFileName);
                    $user->setPhoto('/uploads/' . $newFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_admin_user_edit', ['id' => $user->getId()]);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur mis à jour avec succès.');

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin_user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_user_index');
    }


    #[Route('/attribute-courses/{id}', name: 'admin_attribute_courses')]
    public function attributeCourses(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Fetch the user by ID
        $user = $entityManager->getRepository(User::class)->find($id);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        // Create form (the form does not need the user passed as an option)
        $form = $this->createForm(AttributeCourseType::class);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Get selected courses from the form
            $courses = $form->get('courses')->getData();
    
            // Attribute courses to the user
            foreach ($courses as $course) {
                $user->addCourse($course); // Make sure addCourse() method works correctly
            }
    
            // Persist and flush the user with their updated courses
            try {
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                dump($e->getMessage()); // Display any errors that occur during flush
                exit;
            }
    
            // Add flash message and redirect to admin dashboard after success
            $this->addFlash('success', 'Courses successfully attributed to the user!');
            return $this->redirectToRoute('app_admin_dashboard');
        }
    
        return $this->render('admin_user/attribute_courses.html.twig', [
            'form' => $form->createView(),
            'user' => $user, // Pass the user to the template for context
        ]);
    }
    

}
