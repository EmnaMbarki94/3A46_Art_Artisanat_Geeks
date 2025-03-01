<?php

namespace App\Repository;

use App\Entity\PieceArt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PieceArt>
 */
class PieceArtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PieceArt::class);
    }

    // src/Repository/PieceArtRepository.php
    public function countRecentArtPieces(int $days): int
    {
        $dateThreshold = new \DateTime();
        $dateThreshold->modify('-' . $days . ' days');

        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)') // Use COUNT to ensure a single result
            ->andWhere('p.dateCrea >= :dateThreshold')
            ->setParameter('dateThreshold', $dateThreshold)
            ->getQuery()
            ->getSingleScalarResult(); 
    }

//    /**
//     * @return PieceArt[] Returns an array of PieceArt objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PieceArt
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
