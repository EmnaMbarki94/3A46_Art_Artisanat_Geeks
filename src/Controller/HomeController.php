<?php

namespace App\Controller;

use App\Repository\GalerieRepository;
use App\Repository\PieceArtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

    #[Route('/admin/galeries', name: 'app_galerie_indexA')]
    public function galerieIndex(GalerieRepository $galerieRepository): Response
    {
        $galeries = $galerieRepository->findAll();

        return $this->render('galerie/index.html.twig', [
            'galeries' => $galeries,
        ]);
    }
    #[Route('/admin/piece-art', name: 'app_piece_art_index', methods: ['GET'])]
    public function piecesIndex(PieceArtRepository $pieceArtRepository): Response
    {
        $piece_arts = $pieceArtRepository->findAll();

        return $this->render('piece_art/index.html.twig', [
            'piece_arts' => $piece_arts,
        ]);
    }
}
