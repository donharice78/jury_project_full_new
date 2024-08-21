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
    /**
     * Affiche la liste des utilisateurs.
     *
     * Cette méthode est associée à la route "/admin/user" avec la méthode HTTP GET.
     * Elle récupère tous les utilisateurs depuis le dépôt et les passe à la vue pour affichage.
     *
     * @param UserRepository $userRepository Le dépôt des utilisateurs.
     * @return Response La réponse HTTP contenant la vue.
     */
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        // Récupère tous les utilisateurs depuis la base de données et les rend dans la vue 'admin_user/index.html.twig'
        return $this->render('admin_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * Crée un nouvel utilisateur.
     *
     * Cette méthode est associée à la route "/admin/user/new" avec les méthodes HTTP GET et POST.
     * Elle gère la création d'un nouvel utilisateur via un formulaire.
     *
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour la persistance en base de données.
     * @return Response La réponse HTTP contenant la vue ou une redirection.
     */
    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle instance d'utilisateur
        $user = new User();
        // Création du formulaire pour l'ajout d'un utilisateur
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Traitement de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Validation des rôles de l'utilisateur
            $roles = $user->getRoles();
            if (!is_array($roles)) {
                throw new \Exception('Les rôles doivent être un tableau');
            }

            // Gestion du téléchargement de la photo
            $photo = $form->get('photo')->getData();
            if ($photo) {
                // Génération d'un nouveau nom de fichier et déplacement du fichier téléchargé
                $newFileName = uniqid() . '.' . $photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                    // Définition du chemin de la photo pour l'utilisateur
                    $user->setPhoto('/uploads/' . $newFileName);
                } catch (FileException $e) {
                    // Gestion des erreurs lors du téléchargement de la photo
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_admin_user_new');
                }
            }

            // Persistance du nouvel utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection vers la liste des utilisateurs avec un message de succès
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affichage du formulaire pour la création d'un nouvel utilisateur
        return $this->render('admin_user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche les détails d'un utilisateur spécifique.
     *
     * Cette méthode est associée à la route "/admin/user/{id}" avec la méthode HTTP GET.
     * Elle récupère un utilisateur spécifique et le passe à la vue pour affichage.
     *
     * @param User $user L'utilisateur à afficher.
     * @return Response La réponse HTTP contenant la vue.
     */
    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        // Affichage des détails de l'utilisateur dans la vue 'admin_user/show.html.twig'
        return $this->render('admin_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Modifie les informations d'un utilisateur existant.
     *
     * Cette méthode est associée à la route "/admin/user/{id}/edit" avec les méthodes HTTP GET et POST.
     * Elle gère l'édition des informations d'un utilisateur via un formulaire.
     *
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @param User $user L'utilisateur à modifier.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour la persistance en base de données.
     * @return Response La réponse HTTP contenant la vue ou une redirection.
     */
    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire pour l'édition d'un utilisateur
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Traitement de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du téléchargement de la photo
            $photo = $form->get('photo')->getData();
            if ($photo) {
                // Génération d'un nouveau nom de fichier et déplacement du fichier téléchargé
                $newFileName = uniqid() . '.' . $photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                    // Définition du chemin de la photo pour l'utilisateur modifié
                    $user->setPhoto('/uploads/' . $newFileName);
                } catch (FileException $e) {
                    // Gestion des erreurs lors du téléchargement de la photo
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_admin_user_edit', ['id' => $user->getId()]);
                }
            }

            // Mise à jour des informations de l'utilisateur en base de données
            $entityManager->flush();

            // Redirection vers la liste des utilisateurs avec un message de succès
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affichage du formulaire pour l'édition d'un utilisateur
        return $this->render('admin_user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un utilisateur spécifique.
     *
     * Cette méthode est associée à la route "/admin/user/{id}" avec la méthode HTTP POST.
     * Elle gère la suppression d'un utilisateur et vérifie la validité du token CSRF.
     *
     * @param Request $request La requête HTTP contenant le token CSRF.
     * @param User $user L'utilisateur à supprimer.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour la persistance en base de données.
     * @return Response La réponse HTTP contenant une redirection.
     */
    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérification de la validité du token CSRF pour la suppression
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // Suppression de l'utilisateur de la base de données
            $entityManager->remove($user);
            $entityManager->flush();

            // Ajout d'un message flash de succès
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        } else {
            // Ajout d'un message flash d'erreur en cas de token CSRF invalide
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        // Redirection vers la liste des utilisateurs avec un message de succès
        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}

