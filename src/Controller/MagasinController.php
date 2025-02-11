<?php

namespace App\Controller;

use App\Entity\Magasin;
use App\Form\MagasinType;
use App\Repository\MagasinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/magasin')]
final class MagasinController extends AbstractController
{
    #[Route(name: 'app_magasin_index', methods: ['GET'])]
    public function index(MagasinRepository $magasinRepository): Response
    {
        return $this->render('magasin/index.html.twig', [
            'magasins' => $magasinRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_magasin_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $magasin = new Magasin();
    $form = $this->createForm(MagasinType::class, $magasin);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle file upload if there is a file in the 'photo_m' field
        /** @var UploadedFile $file */
        $file = $form->get('photoM')->getData();

        if ($file) {
            // Generate a unique file name and move the file to the desired directory
            $fileName = uniqid() . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('magasin_directory'), // Make sure this parameter is set in config/services.yaml
                $fileName
            );

            // Set the file name to the 'photo_m' property of the Magasin entity
            $magasin->setPhotoM($fileName);
        }

        // Persist the Magasin entity to the database
        $entityManager->persist($magasin);
        $entityManager->flush();

        // Redirect to the index page
        return $this->redirectToRoute('app_magasin_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('magasin/new.html.twig', [
        'magasin' => $magasin,
        'form' => $form,
    ]);
}

    /* #[Route('/new', name: 'app_magasin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $magasin = new Magasin();
        $form = $this->createForm(MagasinType::class, $magasin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($magasin);
            $entityManager->flush();

            return $this->redirectToRoute('app_magasin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('magasin/new.html.twig', [
            'magasin' => $magasin,
            'form' => $form,
        ]);
    }*/


    #[Route('/{id}', name: 'app_magasin_show', methods: ['GET'])]
    public function show(Magasin $magasin): Response
    {
        return $this->render('magasin/show.html.twig', [
            'magasin' => $magasin,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_magasin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Magasin $magasin, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MagasinType::class, $magasin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photoM')->getData();

            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('magasin_directory'), $filename);
                $magasin->setphotoM($filename);
            }
            $entityManager->persist($magasin);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('magasin/edit.html.twig', [
            'magasin' => $magasin,
            'form' => $form->createView(),
        ]);
    }


    /*#[Route('/{id}/edit', name: 'app_magasin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Magasin $magasin, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MagasinType::class, $magasin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_magasin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('magasin/edit.html.twig', [
            'magasin' => $magasin,
            'form' => $form ->createView(),
        ]);
    }*/

    #[Route('/{id}', name: 'app_magasin_delete', methods: ['POST'])]
    public function delete(Request $request, Magasin $magasin, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $magasin->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($magasin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_magasin_index', [], Response::HTTP_SEE_OTHER);
    }
}
