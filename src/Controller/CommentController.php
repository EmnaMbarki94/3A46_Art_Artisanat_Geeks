<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\PieceArt;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment')]
final class CommentController extends AbstractController
{
    #[Route('/index/{pieceArtId}',name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository,EntityManagerInterface $entityManager,int $pieceArtId): Response
    {
        $pieceArt = $entityManager->getRepository(PieceArt::class)->find($pieceArtId);
       
        if (!$pieceArt) {
            throw $this->createNotFoundException('Piece art not found');
        }

        $comments = $commentRepository->findBy(['pieceArt' => $pieceArt]);

        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
            'piece_art' => $pieceArt, 
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        $pieceArt = $comment->getPieceArt();
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
            'piece_art' => $pieceArt,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', ['pieceArtId' => $comment->getPieceArt()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }
    

    // #[Route('/delete/{id}', name: 'app_comment_delete', methods: ['POST'])]
    // public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    // {
    //     $user = $this->getUser();

    //     if ($comment->getUser() !== $user) {
    //         return $this->json(['error' => 'You are not authorized to delete this comment.'], Response::HTTP_FORBIDDEN);
    //     }

    //     $entityManager->remove($comment);
    //     $entityManager->flush();

    //     return $this->json(['success' => 'Commentaire supprimé!']);
    // }


}
