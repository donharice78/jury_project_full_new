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
    #[Route('/', name: 'app_blogs_index', methods: ['GET'])]
    public function index(BlogsRepository $blogsRepository): Response
    {
        return $this->render('blogs/index.html.twig', [
            'blogs' => $blogsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_blogs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Blogs();
        $form = $this->createForm(BlogsType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('image')->getData();
            foreach ($images as $image) {
                $file = md5(uniqid()). '.'. $image->guessExtension();
                $image->move(
                    $this->getParameter('image_directory'),
                    $file
                );


               $img = new Images();
               $img->setName($file);
               $blog->addImage($img);
            }

            
            $entityManager->persist($blog);
            $entityManager->flush();

            return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blogs/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blogs_show', methods: ['GET'])]
    public function show(Blogs $blog): Response
    {
        return $this->render('blogs/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_blogs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blogs $blog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BlogsType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('image')->getData();
            foreach ($images as $image) {
                $file = md5(uniqid()). '.'. $image->guessExtension();
                $image->move(
                    $this->getParameter('image_directory'),
                    $file
                );


               $img = new Images();
               $img->setName($file);
               $blog->addImage($img);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blogs/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blogs_delete', methods: ['POST'])]
    public function delete(Request $request, Blogs $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete/image{id}', name: 'app_blogs_delete_image', methods: ["DELETE"])]
  public function deleteImage(Images $image, Request $request, EntityManagerInterface $em): JsonResponse
  {
      $data = json_decode($request->getContent(), true);
        
      if($this->isCsrfTokenValid('delete'. $image->getId(), $data['_token'])) {
          $imageName = $image->getName();
          $imagePath = $this->getParameter('image_directory'). '/'. $imageName;
          unlink($imagePath);
          $em->remove($image);
          $em->flush();
 
          return new JsonResponse(['success' => 1]);
      }

      return new JsonResponse(['error' => 'Invalid token'], 400);

      
  }

}
