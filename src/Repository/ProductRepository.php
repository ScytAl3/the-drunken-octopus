<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Query
     */
    public function findAllAvailableQuery(): Query
    {
        return $this->findAvailableQuery(true)
            ->getQuery()
        ;
    }

    /**
     * @return Product[] Returns an array of available Product objects
     */
    public function findAllAvailable(): array
    {
        return $this->findAvailableQuery(true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of unavailable Product objects
     */
    public function findAllUnavailable(): array
    {
        return $this->findAvailableQuery(false)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return QueryBuilder
     */
    private function findAvailableQuery($boolean): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.availability = :val')
            ->setParameter('val', $boolean);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
