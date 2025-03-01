<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;

use App\Service\ElevenLabsTtsService;

#[Route('/cours')]
final class CoursController extends AbstractController
{
    #[Route(name: 'app_cours_index', methods: ['GET'])]
    public function index(CoursRepository $coursRepository,  PaginatorInterface $paginator,  Request $request): Response
    {
        $user = $this->getUser();

        if (!$this->isGranted('ROLE_ENSEIGNANT')) {
            $query = $coursRepository->createQueryBuilder('c')->getQuery();
        } else {
            $query = $coursRepository->createQueryBuilder('c')
                ->where('c.user = :user')
                ->setParameter('user', $user)
                ->getQuery();
        }
        $cours = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('cours/index.html.twig', [
            'cours' => $cours,
        ]);
    }
    
    #[Route('2',name: 'app_cours_index2')]
    public function index2(CoursRepository $coursRepository): Response
    {
        return $this->render('cours/index2.html.twig', [
                'cours' => $coursRepository->findAll(),
            ]);
        }

    #[Route('/new', name: 'app_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager , SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }
        $cour = new Cours();
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            $existingImage = $cour->getImage();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('File upload failed');
                }
                $cour->setImage($newFilename);
            }
            else{
                $cour->setImage($existingImage);
            }
            $cour->setUser($this->getUser());
            $entityManager->persist($cour);
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_new', ['id' => $cour->getId()]);
        }

        return $this->render('cours/new.html.twig', [
            'cours' => $cour,
            'form' => $form,
        ]);
    }

    #[Route('/new2', name: 'app_cours_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, EntityManagerInterface $entityManager , SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }
        $cour = new Cours();
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            $existingImage = $cour->getImage();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('File upload failed');
                }
                $cour->setImage($newFilename);
            }
            else{
                $cour->setImage($existingImage);
            }
            $cour->setUser($this->getUser());
            $entityManager->persist($cour);
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_index2', []);
        }

        return $this->render('cours/new2.html.twig', [
            'cours' => $cour,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'app_cours_show', methods: ['GET'])]
    public function show(Cours $cour,ElevenLabsTtsService $ttsService): Response
    {
        $audioFilePath = $ttsService->synthesizeSpeech($cour->getContenuC());
        return $this->render('cours/show.html.twig', [
            'cour' => $cour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cours, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }

    $form = $this->createForm(CoursType::class, $cours);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('uploads_directory'),
                $newFilename
            );

            $cours->setImage($newFilename);
        }

        $entityManager->persist(object: $cours);
        $entityManager->flush();

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('cours/edit.html.twig', [
        'cour' => $cours,
        'form' => $form,
    ]);
}

    #[Route('/{id}/edit2', name: 'app_cours_edit2', methods: ['GET', 'POST'])]
    public function edit2(Request $request, Cours $cours, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }

    $form = $this->createForm(CoursType::class, $cours);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('uploads_directory'),
                $newFilename
            );

            $cours->setImage($newFilename);
        }

        $entityManager->persist(object: $cours);
        $entityManager->flush();

        return $this->redirectToRoute('app_cours_index2', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('cours/edit2.html.twig', [
        'cour' => $cours,
        'form' => $form,
    ]);
    }

    #[Route('/{id}', name: 'app_cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
    {   
        if (!$this->isGranted('ROLE_ENSEIGNANT') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas la permission d'accéder à cette page.");
        }

        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/cours/{id}/download', name: 'cours_download_pdf')]
    public function downloadPdf(Cours $cours): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('chroot', realpath(''));
        
        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView('cours/pdf_template.html.twig', [
            'cours' => $cours
        ]);
        

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="cours_'.$cours->getId().'.pdf"',
        ]);
    }

    #[Route('2/stats', name: 'cours_stats', methods: ['GET'])]
    public function coursStats(CoursRepository $coursRepository): Response
    {
        $coursesWithQuiz = $coursRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.quiz IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        $coursesWithoutQuiz = $coursRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.quiz IS NULL')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('cours/stats.html.twig', [
            'coursesWithQuiz' => $coursesWithQuiz,
            'coursesWithoutQuiz' => $coursesWithoutQuiz,
        ]);
    }
}
