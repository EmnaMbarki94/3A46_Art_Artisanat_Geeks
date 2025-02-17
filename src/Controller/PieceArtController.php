<?php

namespace App\Controller;

use App\Entity\Galerie;
use App\Entity\PieceArt;
use App\Form\PieceArtType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/piece')]
final class PieceArtController extends AbstractController
{
    #[Route('/index/{galerieId}', name: 'app_piece_art_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, int $galerieId): Response
    {
        $galerie = $entityManager->getRepository(Galerie::class)->find($galerieId);
        if (!$galerie) {
            throw $this->createNotFoundException('Galerie non trouvée');
        }

        $pieceArts = $entityManager->getRepository(PieceArt::class)->findBy(['galerie' => $galerie]);

        return $this->render('piece_art/index.html.twig', [
            'piece_arts' => $pieceArts,
            'galerie' => $galerie,
        ]);
    }

    #[Route('/new/{galerieId}', name: 'app_piece_art_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $galerieId): Response
    {
        $galerie = $entityManager->getRepository(Galerie::class)->find($galerieId);
        if (!$galerie) {
            throw $this->createNotFoundException('Galerie non trouvée');
        }

        $user = $this->getUser();
        if ($galerie->getUser() !== $user) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à ajouter une pièce d\'art à cette galerie.');
            return $this->redirectToRoute('app_galerie_show', ['id' => $galerieId]);
        }

        $pieceArt = new PieceArt();
        $pieceArt->setGalerie($galerie); 
        $form = $this->createForm(PieceArtType::class, $pieceArt, [
            'galerie' => $galerie, // Pass the selected gallery
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $file = $form->get('photoP')->getData();
            if ($file) {
                $fileName = uniqid().'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('photos_directory'), // Assurez-vous que ce paramètre est défini
                    $fileName
                );
                $pieceArt->setPhotoP($fileName);

            }
            // Associer la pièce d'art à la galerie
            
            $entityManager->persist($pieceArt);
            $entityManager->flush();

            $this->addFlash('success', 'La pièce d\'art a été créée avec succès.');

            return $this->redirectToRoute('app_galerie_show', ['id' => $galerieId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('piece_art/new.html.twig', [
            'piece_art' => $pieceArt,
            'form' => $form,
            'galerie' => $galerie,
        ]);
    }

    #[Route('/{id}', name: 'app_piece_art_show', methods: ['GET'])]
    public function show(PieceArt $pieceArt): Response
    {
        return $this->render('piece_art/show.html.twig', [
            'piece_art' => $pieceArt,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_piece_art_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PieceArt $pieceArt, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'You must be logged in to edit a piece d\'art.');
        }
        $galerie = $pieceArt->getGalerie();
        if (!$galerie || $galerie->getUser() !== $user) {
            $this->addFlash('error', 'You do not have permission to edit this piece d\'art.');
            return $this->redirectToRoute('app_galerie_index');
        }
        $form = $this->createForm(PieceArtType::class, $pieceArt, [
            'galerie' => $galerie,  // Pass the associated galerie
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photoP')->getData();
            if ($file) {
                // Gérer l'upload de la photo ici
                $filename = uniqid().'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('photos_directory'), // Assurez-vous que ce paramètre est défini dans services.yaml
                    $filename
                );
                // Mettez à jour le champ photoP avec le nouveau nom de fichier
                $pieceArt->setPhotoP($filename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_galerie_show', ['id' => $pieceArt->getGalerie()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('piece_art/edit.html.twig', [
            'piece_art' => $pieceArt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_piece_art_delete', methods: ['POST'])]
    public function delete(Request $request, PieceArt $pieceArt, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pieceArt->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pieceArt);
            $entityManager->flush();
        }
        $this->addFlash('success', 'La pièce d\'art a été supprimée avec succès.');
        return $this->redirectToRoute('app_galerie_show', ['id' => $pieceArt->getGalerie()->getId()], Response::HTTP_SEE_OTHER);
    }
}