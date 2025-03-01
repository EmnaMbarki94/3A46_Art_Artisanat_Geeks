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
use App\Repository\ReservationRepository;


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
    public function listeF(Request $request, EventRepository $eventRepository): Response
    {
        // Récupérer l'option de tri choisie par l'utilisateur
        $sortOption = $request->query->get('sort', 'default'); // Valeur par défaut 'default'
    
        // Appliquer le tri en fonction de l'option choisie
        if ($sortOption == 'date') {
            // Trier par date croissante
            $events = $eventRepository->findBy([], ['dateE' => 'ASC']);
        } elseif ($sortOption == 'price') {
            // Trier par prix croissant
            $events = $eventRepository->findBy([], ['prixS' => 'ASC']);
        } else {
            // Trier par défaut (si aucune option de tri)
            $events = $eventRepository->findAll();
        }
    
        // Passer les événements triés et l'option de tri à la vue
        return $this->render('evenement/evenement.html.twig', [
            'events' => $events,
            'sortOption' => $sortOption, // Passer l'option de tri à la vue pour la gestion dynamique du select
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
                $file->move($this->getParameter('photos_events_directory'), $filename);
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
                $file->move($this->getParameter('photos_events_directory'), $filename);
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
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->get('_token'))) {
            // Supprimer l'événement (les réservations liées seront supprimées grâce à 'onDelete' CASCADE)
            $entityManager->remove($event);
            $entityManager->flush();
        }
    
        // Rediriger vers la page des événements
        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
     }
     
     #[Route('/stats/top-events', name: 'stats_top_events')]
     public function statistiques(ReservationRepository $reservationRepository, EventRepository $eventRepository): Response
     {
         // 🔹 Récupérer les événements les plus réservés
         $topEventsByReservation = $reservationRepository->getTopEventsByReservationCount();
         
         // 🔹 Récupérer les événements les plus chers
         $mostExpensiveEvents = $eventRepository->getMostExpensiveEvents();
     
         // 🔹 Récupérer les événements par type
         $eventsByType = $eventRepository->getEventsCountByType();
         
         // Préparation des données pour les graphiques
         $eventLabels = [];
         $eventData = [];
     
         foreach ($eventsByType as $event) {
             $eventLabels[] = $event['typeE'];
             $eventData[] = $event['eventCount'];
         }
     
         return $this->render('evenement/StatE.html.twig', [
             'topEventsByReservation' => $topEventsByReservation,
             'mostExpensiveEvents' => $mostExpensiveEvents, // Nouvel ajout
             'eventLabels' => $eventLabels,
             'eventData' => $eventData,
         ]);
     }
     
     

     
}
