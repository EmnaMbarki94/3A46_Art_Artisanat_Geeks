<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GalerieController extends AbstractController{
    #[Route('/galerie', name: 'app_galerie')]
    public function index(): Response
    {
        return $this->render('galerie/galerie.html.twig', [
            'controller_name' => 'GalerieController',
        ]);
    }
    #[Route('/ajoutgalerie', name: 'ajoutGalerie')]
    public function ajoutGalerie(): Response
    {
        return $this->render('galerie/ajoutGalerie.html.twig', [
            'controller_name' => 'GalerieController',
        ]);
    }
}
