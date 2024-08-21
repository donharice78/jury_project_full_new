<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
public function index1(): Response
{
    // Affiche la page d'accueil
    return $this->render('home/index.html.twig');
}

#[Route('/privacy', name: 'app_privacy')]
public function privacy(): Response
{
    // Affiche la page de politique de confidentialité
    return $this->render('home/privacy.html.twig');
}

#[Route('/faq', name: 'app_faq')]
public function faq(): Response
{
    // Affiche la page des questions fréquemment posées (FAQ)
    return $this->render('home/faq.html.twig');
}

#[Route('/certification', name: 'app_certification')]
public function certification(): Response
{
    // Affiche la page de certification
    return $this->render('home/certification.html.twig');
}

#[Route('/financements', name: 'app_financements')]
public function financements(): Response
{
    // Affiche la page des options de financement
    return $this->render('home/financements.html.twig');
}

#[Route('/modaliteacces', name: 'app_modaliteacces')]
public function modaliteAcces(): Response
{
    // Affiche la page des modalités d'accès
    return $this->render('home/modaliteacces.html.twig');
}

#[Route('/evenements', name: 'app_evenements')]
public function evenements(): Response
{
    // Affiche la page des événements
    return $this->render('home/evenements.html.twig');
}

#[Route('/team', name: 'app_team')]
public function team(): Response
{
    // Affiche la page de l'équipe avec les informations des membres
    $teamMembers = [
        [
            'name' => 'Alice Dupont',
            'role' => 'Directrice',
            'bio' => 'Alice a plus de 20 ans d\'expérience dans l\'éducation et dirige notre école avec passion et dévouement.',
            'image' => 'Direc.jpg',
        ],
        [
            'name' => 'Bob Martin',
            'role' => 'Formateur',
            'bio' => 'Bob est un expert en mathématiques avec une approche innovante de l\'enseignement.',
            'image' => 'Bob.jpg',
        ],
        [
            'name' => 'Claire Dubois',
            'role' => 'Formatrice',
            'bio' => 'Claire est spécialisée dans l\'enseignement du HTML et CSS et a une approche créative.',
            'image' => 'claire.jpg',
        ],
        [
            'name' => 'David Leroy',
            'role' => 'Conseiller Pédagogique',
            'bio' => 'David aide les élèves à atteindre leurs objectifs académiques et personnels grâce à ses conseils avisés.',
            'image' => 'david.jpg',
        ],
        [
            'name' => 'Emma Lefevre',
            'role' => 'Formatrice',
            'bio' => 'Emma est spécialisée dans l\'enseignement du JavaScript et ReactJS et a une approche créative.',
            'image' => 'Emma.jpg',
        ],
        [
            'name' => 'François Bernard',
            'role' => 'Responsable Administratif',
            'bio' => 'François assure le bon fonctionnement administratif de l\'école et soutient l\'équipe éducative.',
            'image' => 'francois.jpg',
        ],
    ];
    return $this->render('home/team.html.twig',[
        'team_members' => $teamMembers,
    ]);
}

#[Route('/historie', name: 'app_historie')]
public function historie(): Response
{
    // Affiche la page sur l'histoire de l'organisation
    return $this->render('home/historie.html.twig');
}
}