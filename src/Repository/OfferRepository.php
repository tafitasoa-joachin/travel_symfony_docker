<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }
    /**
     * Récupère des destinations uniques.
     *
     * @return array
     */
    public function findDistinctDestinations(): array
    {
        $results = $this->createQueryBuilder('o')
            ->select('DISTINCT o.destination')
            ->orderBy('o.destination', 'ASC')
            ->getQuery()
            ->getScalarResult();

        // Extraire les destinations sous forme de tableau simple
        return array_map(fn($item) => $item['destination'], $results);
    }
    //    /**
    //     * @return Offer[] Returns an array of Offer objects
    //     */
    // public function findByExampleField($value): array
    // {
    //     return $this->createQueryBuilder('o')
    //         ->andWhere('o.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('o.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    //    public function findOneBySomeField($value): ?Offer
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
