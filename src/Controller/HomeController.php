<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ReclamationRepository; // Ajoute cette ligne

final class HomeController extends AbstractController{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/galerie', name: 'app_galerie')]
    public function galerie(): Response
    {
        return $this->render('galerie/galerie.html.twig', [
            'controller_name' => 'GalerieController',
        ]);
    }

    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        return $this->render('home/admin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/reclamation2', name: 'app_admin2')]
public function Reclamation(ReclamationRepository $reclamationRepository): Response
{
    return $this->render('reclamation/index2.html.twig', [
        'controller_name' => 'ReclamationController',
        'reclamations' => $reclamationRepository->findAll(), // Ajoute cette ligne
    ]);
}
}
