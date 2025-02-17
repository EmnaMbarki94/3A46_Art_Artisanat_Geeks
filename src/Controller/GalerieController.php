<?php

namespace App\Controller;

use App\Entity\Galerie;
use App\Entity\PieceArt;
use App\Form\GalerieType;
use App\Repository\GalerieRepository;
use App\Repository\PieceArtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/galerie')]
final class GalerieController extends AbstractController{
    #[Route(name: 'app_galerie_index', methods: ['GET'])]
    public function index(GalerieRepository $galerieRepository): Response
    {
        return $this->render('galerie/galerie.html.twig', [
            'galeries' => $galerieRepository->findAll(),
        ]);
    }

    #[IsGranted("ROLE_ARTISTE")]
    #[Route('/new', name: 'app_galerie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $galerie = new Galerie();
        $form = $this->createForm(GalerieType::class, $galerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser(); // Get the currently authenticated user
            if ($user) {
                $galerie->setUser($user); // Associate the gallery with the user
            }else {
               
                return $this->redirectToRoute('app_login');
            }
        
            $file = $form->get('photoG')->getData();

            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('photos_directory'), $filename);
                $galerie->setPhotoG($filename);
            }
    
            $entityManager->persist($galerie);
            $entityManager->flush();

            $this->addFlash('success', 'Galerie ajoutée avec succès.');

            return $this->redirectToRoute('app_galerie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('galerie/new.html.twig', [
            'galerie' => $galerie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_galerie_show', methods: ['GET'])]
    public function show(Galerie $galerie, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les pièces d'art associées à la galerie
        $pieceArts = $entityManager->getRepository(PieceArt::class)->findBy(['galerie' => $galerie]);

        return $this->render('galerie/show.html.twig', [
            'galerie' => $galerie,
            'pieceArts' => $pieceArts,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_galerie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Galerie $galerie, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'You must be logged in to edit a gallery.');
            return $this->redirectToRoute('app_galerie_index'); 
        }
    
        // Ensure the user is the owner of the gallery
        if ($galerie->getUser() !== $user) {
            $this->addFlash('error', 'You do not have permission to edit this gallery.');
            return $this->redirectToRoute('app_galerie_index');
        }
        
        $form = $this->createForm(GalerieType::class, $galerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photoG')->getData();
            if ($file) {
                // Gérer l'upload de la photo ici
                $filename = uniqid().'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('photos_directory'), // Assurez-vous que ce paramètre est défini dans services.yaml
                    $filename
                );
                // Mettez à jour le champ photoG avec le nouveau nom de fichier
                $galerie->setPhotoG($filename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_galerie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('galerie/edit.html.twig', [
            'galerie' => $galerie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_galerie_delete', methods: ['POST'])]
    public function delete(Request $request, Galerie $galerie, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user) {
            $entityManager->refresh($user);
        }

        // Check if the user is authenticated and the owner of the gallery
        if (!$user || $galerie->getUser() !== $user) {
            $this->addFlash('error', 'You do not have permission to delete this gallery.');
            return $this->redirectToRoute('app_galerie_index');
        }
    
        // Check CSRF token for security
        if ($this->isCsrfTokenValid('delete' . $galerie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($galerie); // Remove the gallery
            $entityManager->flush(); // Persist the deletion
        }
        $this->addFlash('success', 'Galerie supprimée avec succès.');
        return $this->redirectToRoute('app_galerie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/galeries', name: 'app_galerie_indexA')]
    public function indexAdmin(GalerieRepository $galerieRepository): Response
    {
        $galeries = $galerieRepository->findAll();

        return $this->render('galerie/index.html.twig', [
            'galeries' => $galeries,
        ]);
    }
    #[Route('/galerie/{id}/pieces-art', name: 'app_galerie_pieces_art', methods: ['GET'])]
    public function piecesArt(Galerie $galerie, PieceArtRepository $pieceArtRepository): Response
    {
        $piecesArt = $pieceArtRepository->findBy(['galerie' => $galerie]);

        return $this->render('piece_art/index.html.twig', [
            'piece_arts' => $piecesArt,
            'galerie' => $galerie,
        ]);
    }
}
