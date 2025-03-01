<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
    #[Route('/calendar', name: 'app_calendar', methods: ['GET', 'POST'])]
    public function calendar(): Response
    {
        return $this->render('reservation/calendar.html.twig');
    }

    #[Route('/evenement/{id}', name: 'app_evenement_reserve', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupère l'événement
        $event = $entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        // Récupère toutes les réservations liées à cet événement
        $reservations = $entityManager->getRepository(Reservation::class)
            ->findBy(['relation' => $event]);

        $reservation = new Reservation();
        $reservation->setRelation($event); // Associe l'événement sélectionné

        // Crée le formulaire de réservation
        $form = $this->createForm(ReservationType::class, $reservation, [
            'relation' => $event, // Passe l'événement dans les options
        ]);

        // Récupère l'utilisateur authentifié
        $user = $this->getUser();
        if ($user) {
            $reservation->setUserId($user); // Associe l'utilisateur à la réservation
        }

        // Gère la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère le nombre de places réservées
            $nbPlacesReservees = $reservation->getNbPlace();

            // Vérifie si le nombre de places disponibles est suffisant
            if ($event->getNbTicket() >= $nbPlacesReservees) {
                // Décrémente le nombre de tickets
                $event->setNbTicket($event->getNbTicket() - $nbPlacesReservees);

                // Persiste la réservation
                $entityManager->persist($reservation);

                // Persiste l'événement mis à jour
                $entityManager->persist($event);

                // Commit les changements dans la base de données
                $entityManager->flush();

                // Ajoute un message de succès
                $this->addFlash('success', 'Votre réservation a été effectuée avec succès !');

                // Redirige vers la même page ou une autre page
                return $this->redirectToRoute('app_evenement_reserve', ['id' => $event->getId()]);
            } else {
                // Si le nombre de places disponibles est insuffisant
                $this->addFlash('error', 'Il n\'y a pas assez de places disponibles.');
                return $this->redirectToRoute('app_evenement_reserve', ['id' => $event->getId()]);
            }
        }

        // Affiche la vue
        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
            'event' => $event,
            'reservations' => $reservations, // Passer les réservations liées à l'événement
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
