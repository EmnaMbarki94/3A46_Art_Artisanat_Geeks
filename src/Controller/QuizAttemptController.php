<?php

namespace App\Controller;

use App\Entity\QuizAttempt;
use App\Form\QuizAttemptType;
use App\Repository\QuizAttemptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/quiz/attempt')]
final class QuizAttemptController extends AbstractController
{
    #[Route(name: 'app_quiz_attempt_index', methods: ['GET'])]
    public function index(QuizAttemptRepository $quizAttemptRepository): Response
    {
        return $this->render('quiz_attempt/index.html.twig', [
            'quiz_attempts' => $quizAttemptRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_quiz_attempt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quizAttempt = new QuizAttempt();
        $form = $this->createForm(QuizAttemptType::class, $quizAttempt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quizAttempt->setUser($this->getUser());
            $entityManager->persist($quizAttempt);
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_attempt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz_attempt/new.html.twig', [
            'quiz_attempt' => $quizAttempt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_attempt_show', methods: ['GET'])]
    public function show(QuizAttempt $quizAttempt): Response
    {
        return $this->render('quiz_attempt/show.html.twig', [
            'quiz_attempt' => $quizAttempt,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_attempt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QuizAttempt $quizAttempt, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuizAttemptType::class, $quizAttempt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_attempt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz_attempt/edit.html.twig', [
            'quiz_attempt' => $quizAttempt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_attempt_delete', methods: ['POST'])]
    public function delete(Request $request, QuizAttempt $quizAttempt, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quizAttempt->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quizAttempt);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quiz_attempt_index', [], Response::HTTP_SEE_OTHER);
    }
}
