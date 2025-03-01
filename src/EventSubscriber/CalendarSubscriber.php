<?php
namespace App\EventSubscriber;

use App\Repository\ReservationRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Event\CalendarEvent;
use CalendarBundle\Entity\Event as CalendarEventEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalendarSubscriber extends AbstractController implements EventSubscriberInterface
{
    private ReservationRepository $reservationRepository;
    private UrlGeneratorInterface $router;

    public function __construct(ReservationRepository $reservationRepository, UrlGeneratorInterface $router)
    {
        $this->reservationRepository = $reservationRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar): void
    {
        $user = $this->getUser();
        if (!$user) {
            return;
        }

        $reservations = $this->reservationRepository->createQueryBuilder('r')
            ->join('r.user_id', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getResult();

        foreach ($reservations as $reservation) {
            $dateE = $reservation->getRelation()->getDateE();
            $nom = $reservation->getRelation()->getNom();
            $description = $reservation->getRelation()->getInfoE();
            $etat_e = $reservation->getEtatE();

            // Définition des couleurs et icônes en fonction du type de réservation
            $backgroundColor = match ($etat_e) {
                'VIP' => ' #d4af37 ', // Or
                'Simple' => '#8b008b', // Violet
                'Annulée' => '#DC143C', // Rouge foncé pour une réservation annulée
                default => '#4682B4' // Bleu acier pour le reste
            };

            $icon = match ($etat_e) {
                'VIP' => '⭐', // Icône étoile pour VIP
                'Simple' => '🔹', // Icône bleu pour standard
                'Annulée' => '❌', // Icône croix pour annulé
                default => '📅'
            };

            // Création de l'événement
            $eventEntity = new CalendarEventEntity("$icon $nom", $dateE);

            $eventEntity->setOptions([
                'backgroundColor' => $backgroundColor,
                'borderColor' => '#660066',
                'textColor' => 'black',
                'editable' => false,
                'allDay' => false,
                'start' => $dateE->format('Y-m-d H:i:s'),
            ]);

            // Ajouter des propriétés supplémentaires pour affichage avancé
            $eventEntity->addOption('extendedProps', [
                'nom' => $nom,
                'date' => $dateE->format('d-m-Y H:i'),
                'type' => $etat_e,
                'description' => $description,
            ]);

            // Ajouter l'événement au calendrier
            $calendar->addEvent($eventEntity);
        }
    }
}
