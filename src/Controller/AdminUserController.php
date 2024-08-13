<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/user')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $roles = $user->getRoles();
        if (!is_array($roles)) {
            throw new \Exception('Roles must be an array');
        }

        $photo = $form->get('photo')->getData();

            if ($photo) {
                // Génère un nouveau nom de fichier et déplace le fichier téléchargé
                $newFileName = uniqid() . '.' . $photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                    // Définit le chemin de l'image pour le nouveau cours
                    $user->setPhoto('/uploads/' . $newFileName);
                } catch (FileException $e) {
                    // Gère l'exception en cas de problème lors du téléchargement du fichier
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage());
                    return $this->redirectToRoute('app_user');
                }
            }


            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin_user/show.html.twig', [
            'user' => $user,
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
                // Génère un nouveau nom de fichier et déplace le fichier téléchargé
                $newFileName = uniqid() . '.' . $photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                    // Définit le chemin de l'image pour le nouveau cours
                    $user->setPhoto('/uploads/' . $newFileName);
                } catch (FileException $e) {
                    // Gère l'exception en cas de problème lors du téléchargement du fichier
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage());
                    return $this->redirectToRoute('app_user');
                }
            }



            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifie la validité du token CSRF pour la suppression
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            // Ajoute un message flash de succès
            $this->addFlash('success', 'Le cours a été supprimé avec succès.');
        } else {
            // Ajoute un message flash d'erreur en cas de token CSRF invalide
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
