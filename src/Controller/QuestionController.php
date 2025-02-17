<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


#[Route('/question')]
final class QuestionController extends AbstractController
{
    #[Route(name: 'app_question_index', methods: ['GET'])]
    public function index(QuestionRepository $questionRepository): Response
    {
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }
        return $this->render('question/index.html.twig', [
            'questions' => $questionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }
        $question = new Question();
        
        $quizId = $request->query->get('id');
        //dd($quizId);
        $quiz = $entityManager->getRepository(Quiz::class)->find($quizId);
        $question->setQuiz($quiz);


        $form = $this->createForm(QuestionType::class, $question,[
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_question_new', ['id' => $quizId]);
        }

        return $this->render('question/new.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }

    #[Route('', name: 'app_question_show', methods: ['GET'])]
    public function show(Question $question): Response
    {
        return $this->render('question/show.html.twig', [
            'question' => $question,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }
        $form = $this->createForm(QuestionType::class, $question,[
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);
        $quizId = $form->get('quiz')->getData()->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_show',['id'=> $quizId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_question_delete', methods: ['POST'])]
    public function delete(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }
}
