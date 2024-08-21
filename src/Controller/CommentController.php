<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/comment')]
class CommentController extends AbstractController
{
    /**
     * Affiche un aperçu des derniers commentaires.
     *
     * @param CommentRepository $commentRepository Le dépôt des commentaires.
     * @return Response La réponse contenant la vue partielle des commentaires.
     */
    #[Route('/comments/partial', name: 'app_comments_partial')]
    public function commentsPartial(CommentRepository $commentRepository): Response
    {
        // Récupère les 5 derniers commentaires depuis la base de données
        $comments = $commentRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('comment/_comments.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * Affiche la liste de tous les commentaires.
     *
     * @param CommentRepository $commentRepository Le dépôt des commentaires.
     * @return Response La réponse contenant la vue de tous les commentaires.
     */
    #[Route('/', name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        // Récupère tous les commentaires depuis la base de données
        $comments = $commentRepository->findAll();

        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * Crée un nouveau commentaire.
     *
     * @param Request $request La requête HTTP.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités.
     * @param Security $security Le service de sécurité pour obtenir l'utilisateur authentifié.
     * @return Response La réponse contenant la vue ou une redirection.
     */
    #[Route('/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Assure que seuls les utilisateurs authentifiés peuvent commenter
            $user = $security->getUser();
            if ($user) {
                $comment->setUser($user);
                $entityManager->persist($comment);
                $entityManager->flush();

                return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
            }

            // Gère le cas où l'utilisateur n'est pas authentifié
            $this->addFlash('error', 'Vous devez être connecté pour commenter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche les détails d'un commentaire spécifique.
     *
     * @param Comment $comment Le commentaire à afficher.
     * @return Response La réponse contenant la vue du commentaire.
     */
    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * Modifie un commentaire existant.
     *
     * @param Request $request La requête HTTP.
     * @param Comment $comment Le commentaire à modifier.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités.
     * @return Response La réponse contenant la vue ou une redirection.
     */
    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    /**
     * Supprime un commentaire spécifique.
     *
     * @param Request $request La requête HTTP.
     * @param Comment $comment Le commentaire à supprimer.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités.
     * @return Response Une redirection.
     */
    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        // Vérifie la validité du token CSRF pour la suppression
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
