<?php

namespace App\Repository;

use App\Entity\Bootle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Bootle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bootle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bootle[]    findAll()
 * @method Bootle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BootleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bootle::class);
    }

    // /**
    //  * @return Bootle[] Returns an array of Bootle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bootle
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
