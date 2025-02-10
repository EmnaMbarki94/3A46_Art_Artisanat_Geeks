<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/evenement')]
final class EvenementController extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }
    #[Route('/listeevenementF', name: 'app_evenement_listeF', methods: ['GET'])]
    public function listeF(EventRepository $eventRepository): Response
    {
        return $this->render('evenement/evenement.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photoE')->getData();

            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
<<<<<<< HEAD
                $file->move($this->getParameter('photos_directory'), $filename);
=======
                $file->move($this->getParameter('photos_events_directory'), $filename);
>>>>>>> 67ede58c13395a5f2206417896f7661b70eb0dd6
                $event->setPhotoE($filename);
            }
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('evenement/show.html.twig', [
            'event' => $event,
        ]);
    }
    #[Route('/listeevenementF/{id}', name: 'app_evenement_showdetail', methods: ['GET'])]
    public function detail(Event $event): Response
    {
        return $this->render('evenement/showdetail.html.twig', [
            'event' => $event,
        ]);
    }
    

    
    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photoE')->getData();

            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
<<<<<<< HEAD
                $file->move($this->getParameter('photos_directory'), $filename);
=======
                $file->move($this->getParameter('photos_events_directory'), $filename);
>>>>>>> 67ede58c13395a5f2206417896f7661b70eb0dd6
                $event->setPhotoE($filename);
            }
            $entityManager->persist($event);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
