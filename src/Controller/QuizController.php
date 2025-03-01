<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Cours;
use App\Entity\User;
use App\Entity\Question;
use App\Entity\QuizAttempt;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


#[Route('/quiz')]
final class QuizController extends AbstractController
{
    #[Route(name: 'app_quiz_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //access deny
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }
        $quiz = new Quiz();

        $courseId = $request->query->get('id');
        $selectedCourse = null;
        if ($courseId) {
            $selectedCourse = $entityManager->getRepository(Cours::class)->find($courseId);
            $quiz->setCours($selectedCourse);
        }
        
        $form = $this->createForm(QuizType::class, $quiz, [
            'selected_course' => $selectedCourse,
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedCourse = $quiz->getCours();

            $selectedCourse->setQuiz($quiz);
            $entityManager->persist($selectedCourse);
            
            $entityManager->persist($quiz);
            $entityManager->flush();

            return $this->redirectToRoute('app_question_new', ['id' => $quiz->getId()]);
        }

        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_show', methods: ['GET'])]
    public function show(Quiz $quiz,EntityManagerInterface $entityManager): Response
    {
        
        $questions=$entityManager->getRepository(Question::class)->findBy(['quiz'=>$quiz]);
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }
        $form = $this->createForm(QuizType::class, $quiz, [
            'selected_course' => $quiz->getCours(),
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_delete', methods: ['POST'])]
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/quiz/{id}/submit', name: 'quiz_submit', methods: ['POST'])]
    public function submitQuiz(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        $questions=$entityManager->getRepository(Question::class)->findBy(['quiz'=>$quiz]);
        $submittedAnswers = $request->request->all();
        $score = $this->calculateScore($quiz, $submittedAnswers,$entityManager);
        
        $quizAttempt = new QuizAttempt();
        $quizAttempt->setUser($this->getUser());
        $quizAttempt->setQuiz($quiz);
        $quizAttempt->setNote($score);

        $entityManager->persist($quizAttempt);

        $user = $this->getUser();
        $currentPoints = $user->getPoint();
        $user->setPoint($currentPoints + $score);
        $entityManager->persist($user);

        
        $entityManager->flush();

        return $this->redirectToRoute('app_quiz_attempt_show', ['id' => $quizAttempt->getId(),'n'=>count($questions)]);
    }

    private function calculateScore(Quiz $quiz, array $submittedAnswers,EntityManagerInterface $entityManager): int
    {
        $score = 0;
        foreach ($entityManager->getRepository(Question::class)->findBy(['quiz'=>$quiz]) as $question) {
            $questionId = $question->getId();
            $correctAnswer = $question->getReponses()[0];
            
            if (isset($submittedAnswers['question_' . $questionId]) && $submittedAnswers['question_' . $questionId] === $correctAnswer) {
                $score++;
            }
        }
        return $score;
    }
}
