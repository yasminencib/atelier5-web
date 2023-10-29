<?php

namespace App\Repository;

use App\Entity\Author11;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author11>
 *
 * @method Author11|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author11|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author11[]    findAll()
 * @method Author11[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Author11Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author11::class);
    }

//    /**
//     * @return Author11[] Returns an array of Author11 objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author11
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
