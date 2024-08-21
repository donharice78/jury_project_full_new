<?php

namespace App\Controller;

use App\Entity\Blogs;
use App\Entity\Images;
use App\Form\BlogsType;
use App\Repository\BlogsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/blogs')]
class BlogsController extends AbstractController
{
    /**
     * Affiche la liste de tous les blogs.
     *
     * @param BlogsRepository $blogsRepository Le dépôt des blogs pour accéder aux données.
     * @return Response La réponse contenant la vue des blogs.
     */
    #[Route('/', name: 'app_blogs_index', methods: ['GET'])]
    public function index(BlogsRepository $blogsRepository): Response
    {
        // Récupère tous les blogs depuis la base de données
        return $this->render('blogs/index.html.twig', [
            'blogs' => $blogsRepository->findAll(),
        ]);
    }

    /**
     * Crée un nouveau blog.
     *
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour persister les données.
     * @return Response La réponse contenant la vue du formulaire ou une redirection en cas de succès.
     */
    #[Route('/new', name: 'app_blogs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Blogs();  // Crée une nouvelle instance de Blogs
        $form = $this->createForm(BlogsType::class, $blog);  // Crée le formulaire pour le blog
        $form->handleRequest($request);  // Traite les données de la requête

        if ($form->isSubmitted() && $form->isValid()) {  // Vérifie si le formulaire est soumis et valide

            $images = $form->get('image')->getData();  // Récupère les images du formulaire
            foreach ($images as $image) {  // Parcourt les images
                $file = md5(uniqid()). '.'. $image->guessExtension();  // Génère un nom de fichier unique
                $image->move(
                    $this->getParameter('image_directory'),  // Répertoire de destination des images
                    $file  // Nouveau nom de fichier
                );

                // Crée une nouvelle instance d'Images et associe l'image au blog
                $img = new Images();
                $img->setName($file);
                $blog->addImage($img);
            }

            $entityManager->persist($blog);  // Prépare l'entité pour l'insertion en base de données
            $entityManager->flush();  // Enregistre les modifications en base de données

            return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);  // Redirection vers la liste des blogs
        }

        return $this->render('blogs/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),  // Passe la vue du formulaire au template
        ]);
    }

    /**
     * Affiche les détails d'un blog spécifique.
     *
     * @param Blogs $blog L'entité Blog à afficher.
     * @return Response La réponse contenant la vue du blog.
     */
    #[Route('/{id}', name: 'app_blogs_show', methods: ['GET'])]
    public function show(Blogs $blog): Response
    {
        return $this->render('blogs/show.html.twig', [
            'blog' => $blog,  // Passe le blog au template
        ]);
    }

    /**
     * Modifie un blog existant.
     *
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @param Blogs $blog L'entité Blog à modifier.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour persister les modifications.
     * @return Response La réponse contenant la vue du formulaire ou une redirection en cas de succès.
     */
    #[Route('/{id}/edit', name: 'app_blogs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blogs $blog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BlogsType::class, $blog);  // Crée le formulaire pour éditer le blog
        $form->handleRequest($request);  // Traite les données de la requête

        if ($form->isSubmitted() && $form->isValid()) {  // Vérifie si le formulaire est soumis et valide

            $images = $form->get('image')->getData();  // Récupère les images du formulaire
            foreach ($images as $image) {  // Parcourt les images
                $file = md5(uniqid()). '.'. $image->guessExtension();  // Génère un nom de fichier unique
                $image->move(
                    $this->getParameter('image_directory'),  // Répertoire de destination des images
                    $file  // Nouveau nom de fichier
                );

                // Crée une nouvelle instance d'Images et associe l'image au blog
                $img = new Images();
                $img->setName($file);
                $blog->addImage($img);
            }

            $entityManager->flush();  // Enregistre les modifications en base de données

            return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);  // Redirection vers la liste des blogs
        }

        return $this->render('blogs/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),  // Passe la vue du formulaire au template
        ]);
    }

    /**
     * Supprime un blog spécifique.
     *
     * @param Request $request La requête HTTP contenant le token CSRF pour la suppression.
     * @param Blogs $blog L'entité Blog à supprimer.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour supprimer le blog.
     * @return Response Une redirection vers la liste des blogs.
     */
    #[Route('/{id}', name: 'app_blogs_delete', methods: ['POST'])]
    public function delete(Request $request, Blogs $blog, EntityManagerInterface $entityManager): Response
    {
        // Vérifie la validité du token CSRF pour la suppression
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $entityManager->remove($blog);  // Prépare le blog pour la suppression
            $entityManager->flush();  // Enregistre la suppression en base de données
        }

        return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);  // Redirection vers la liste des blogs
    }

    /**
     * Supprime une image associée à un blog.
     *
     * @param Images $image L'entité Image à supprimer.
     * @param Request $request La requête HTTP contenant le token CSRF pour la suppression.
     * @param EntityManagerInterface $em Le gestionnaire d'entités pour supprimer l'image.
     * @return JsonResponse La réponse JSON indiquant le succès ou l'échec de l'opération.
     */
    #[Route('/delete/image{id}', name: 'app_blogs_delete_image', methods: ["DELETE"])]
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);  // Décode les données JSON de la requête

        // Vérifie la validité du token CSRF pour la suppression de l'image
        if($this->isCsrfTokenValid('delete'. $image->getId(), $data['_token'])) {
            $imageName = $image->getName();  // Récupère le nom du fichier image
            $imagePath = $this->getParameter('image_directory'). '/'. $imageName;  // Construit le chemin complet de l'image
            unlink($imagePath);  // Supprime le fichier image du serveur
            $em->remove($image);  // Prépare l'image pour la suppression en base de données
            $em->flush();  // Enregistre la suppression en base de données

            return new JsonResponse(['success' => 1]);  // Réponse JSON indiquant le succès de la suppression
        }

        return new JsonResponse(['error' => 'Token invalide'], 400);  // Réponse JSON en cas de token CSRF invalide
    }
}
