<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }
    public function getEventsCountByType(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.typeE, COUNT(e.id) as eventCount')
            ->groupBy('e.typeE')
            ->orderBy('eventCount', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function getMostExpensiveEvents(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.nom, e.prixS')
            ->orderBy('e.prixS', 'DESC') // Trie par prix décroissant
            ->setMaxResults(5) // Limite aux 5 événements les plus chers (modifiable)
            ->getQuery()
            ->getResult();
    }
    




//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
