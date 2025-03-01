<?php

namespace App\Controller;

use App\Entity\Galerie;
use App\Entity\PieceArt;
use App\Entity\Comment;
use App\Form\PieceArtType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\PieceArtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/piece')]
final class PieceArtController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }
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
            else {
                $pieceArt->setPhotoP('default.jpg'); 
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

    #[Route('/{id}', name: 'app_piece_art_show', methods: ['GET', 'POST'])]
    public function show(Request $request, PieceArt $pieceArt, CommentRepository $commentRepository, EntityManagerInterface $entityManager): Response
    {
        $comments = $commentRepository->findBy(['pieceArt' => $pieceArt]);
        $galerie = $pieceArt->getGalerie(); 

        // Create a new comment form
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $user = $this->getUser();
        if (!$user) {
            $galerieId = $galerie ? $galerie->getId() : null;
    
            $this->addFlash('error', 'Vous devez vous connecter pour pouvoir commenter.');
            return $this->redirectToRoute('app_galerie_show', ['id' => $galerieId], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPieceArt($pieceArt);
            $comment->setUser($user);
            $comment->setCreationDate(new \DateTime());

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->json([
                'success' => 'Commentaire ajouté!',
                'comment' => [
                    'id' => $comment->getId(),
                    'user' => [
                        'fullName' => $comment->getUser()->getFirstName() . ' ' . $comment->getUser()->getLastName(),
                        'id' => $comment->getUser()->getId(),
                    ],
                    'creationDate' => $comment->getCreationDate()->format('Y-m-d H:i'),
                    'content' => $comment->getContent(),
                    'deleteUrl' => $this->generateUrl('app_comment_delete', ['id' => $comment->getId()]),
                    'csrfToken' => $this->csrfTokenManager->getToken('delete' . $comment->getId())->getValue(),
                ],
            ]);
        }
        return $this->render('piece_art/show.html.twig', [
            'piece_art' => $pieceArt,
            'comments' => $comments,
            'galerie' => $galerie, 
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_piece_art_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PieceArt $pieceArt, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez s\'authentifier pour éditer une piece d\'art.');
        }
        $galerie = $pieceArt->getGalerie();
        if (!$galerie || ($galerie->getUser() !== $user && !$this->isGranted('ROLE_ADMIN'))) {
            $this->addFlash('error', 'Vous n\'etes pas autorisé à éditer cette piece d\'art.');
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
                    $this->getParameter('photos_directory'), 
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

    #[Route('/{id}/delete', name: 'app_piece_art_delete', methods: ['POST'])]
    public function delete(Request $request, PieceArt $pieceArt, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $galerie = $pieceArt->getGalerie();
        if (!$galerie || ($galerie->getUser() !== $user && !$this->isGranted('ROLE_ADMIN'))) {
            $this->addFlash('error', 'Vous n\'etes pas autorisé à supprimer cette piece d\'art.');
            return $this->redirectToRoute('app_galerie_index');
        }
        if ($this->isCsrfTokenValid('delete'.$pieceArt->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pieceArt);
            $entityManager->flush();
            $this->addFlash('success', 'La pièce d\'art a été supprimée avec succès.');
        }
       
        return $this->redirectToRoute('app_galerie_show', ['id' => $pieceArt->getGalerie()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/comments', name: 'app_piece_comment')]
    public function pieceComments(int $id, PieceArtRepository $pieceArtRepository): Response
    {
        // Fetch the piece of art by its ID
        $pieceArt = $pieceArtRepository->find($id);

        if (!$pieceArt) {
            throw $this->createNotFoundException('Piece art not found');
        }

        return $this->render('comment/index.html.twig', [
            'piece_art' => $pieceArt, 
        ]);
    }

    #[Route('/delete/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function deleteComment(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($comment->getUser() !== $user) {
            return $this->json(['error' => 'You are not authorized to delete this comment.'], Response::HTTP_FORBIDDEN);
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->json(['success' => 'Commentaire supprimé!']);
    }
}