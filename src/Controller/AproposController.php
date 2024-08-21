<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AproposController extends AbstractController
{
    /**
     * Affiche la page "À propos".
     *
     * Cette méthode est associée à la route "/apropos" et renvoie la vue "apropos/index.html.twig".
     *
     * @return Response La réponse HTTP contenant le rendu de la vue.
     */
    #[Route('/apropos', name: 'app_apropos')]
    public function index(): Response
    {
        // Rendu de la vue "apropos/index.html.twig" avec le nom du contrôleur passé à la vue.
        return $this->render('apropos/index.html.twig', [
            'controller_name' => 'AproposController',
        ]);
    }
}
