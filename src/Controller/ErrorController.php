<?php



namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ErrorController extends AbstractController
{
    #[Route('/{catchall}', name: 'handle_undefined_route', requirements: ['catchall' => '.+'])]
    public function handleUndefinedRoute(Request $request): Response
    {
        // Log or handle the undefined route request
        // You can also show a custom 404 page or redirect to a known route

        return $this->render('error/404.html.twig');
    }
}
