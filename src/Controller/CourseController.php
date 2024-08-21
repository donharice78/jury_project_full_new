<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\Course1Type;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/course')]
class CourseController extends AbstractController
{
    /**
     * Affiche la liste de tous les cours.
     *
     * @param CourseRepository $courseRepository Le dépôt des cours.
     * @return Response La réponse contenant la vue.
     */
    #[Route('/', name: 'app_course_index', methods: ['GET'])]
    public function index(CourseRepository $courseRepository): Response
    {
        // Récupère tous les cours depuis la base de données
        return $this->render('course/index.html.twig', [
            'courses' => $courseRepository->findAll(),
        ]);
    }

    /**
     * Crée un nouveau cours.
     *
     * @param Request $request La requête HTTP.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités.
     * @return Response La réponse contenant la vue ou une redirection.
     */
    #[Route('/new', name: 'app_course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Crée une nouvelle instance de Course
        $course = new Course();
        // Crée le formulaire pour ajouter un nouveau cours
        $form = $this->createForm(Course1Type::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();

            if ($image) {
                // Génère un nouveau nom de fichier et déplace le fichier téléchargé
                $newFileName = uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                    // Définit le chemin de l'image pour le nouveau cours
                    $course->setImage('/uploads/' . $newFileName);
                } catch (FileException $e) {
                    // Gère l'exception en cas de problème lors du téléchargement du fichier
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_course_new');
                }
            }

            $entityManager->persist($course);
            $entityManager->flush();

            // Ajoute un message flash de succès
            $this->addFlash('success', 'Le cours a été créé avec succès.');

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    /**
     * Affiche les détails d'un cours spécifique.
     *
     * @param Course $course Le cours à afficher.
     * @return Response La réponse contenant la vue.
     */
    #[Route('/{id}', name: 'app_course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    /**
     * Modifie un cours existant.
     *
     * @param Request $request La requête HTTP.
     * @param Course $course Le cours à modifier.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités.
     * @return Response La réponse contenant la vue ou une redirection.
     */
    #[Route('/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // Crée le formulaire pour éditer le cours
        $form = $this->createForm(Course1Type::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $image = $form->get('image')->getData();

            if ($image) {
                // Génère un nouveau nom de fichier et déplace le fichier téléchargé
                $newFileName = uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                    // Définit le chemin de l'image pour le cours modifié
                    $course->setImage('/uploads/' . $newFileName);
                } catch (FileException $e) {
                    // Gère l'exception en cas de problème lors du téléchargement du fichier
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_course_edit', ['id' => $course->getId()]);
                }
            }

            $entityManager->flush();

            // Ajoute un message flash de succès
            $this->addFlash('success', 'Le cours a été modifié avec succès.');

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    /**
     * Supprime un cours spécifique.
     *
     * @param Request $request La requête HTTP.
     * @param Course $course Le cours à supprimer.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités.
     * @return Response Une redirection.
     */
    #[Route('/{id}', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // Vérifie la validité du token CSRF pour la suppression
        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->request->get('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();

            // Ajoute un message flash de succès
            $this->addFlash('success', 'Le cours a été supprimé avec succès.');
        } else {
            // Ajoute un message flash d'erreur en cas de token CSRF invalide
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
    }
}
