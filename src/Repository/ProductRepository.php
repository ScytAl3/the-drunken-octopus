<?php

namespace App\Repository;

use App\Data\SearchData;
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
     * returns products related to a search
     * 
     * @return Product[] Returns an array of available Product objects
     */
    public function findSearch(SearchData $data): array
    {
        return $this->findAll();
    }

    /**
     * Return a query used for the paginator bundle
     * @return Query 
     */
    public function findSearchQuery(SearchData $searchdata): Query
    {
        // Construction de la requête
        $query = $this
            ->createQueryBuilder('p')
            ->select('s', 'p')
            ->join('p.style', 's')
            ->select('c', 'p')
            ->join('p.country', 'c');
        // Contrôle du champ de recherche
        if (!empty($searchdata->q)) {
            $query = $query
                ->andWhere('p.title LIKE :q')
                ->setParameter('q', "%{$searchdata->q}%");
        }
        // Contrôle du filtre sur les styles
        if (!empty($searchdata->style)) {
            $query = $query
                ->andWhere('s.id IN (:style)')
                ->setParameter('style', $searchdata->style);
        }
        // Contrôle du filtre sur les pays
        if (!empty($searchdata->country)) {
            $query = $query
            ->andWhere('c.id IN (:country)')
                ->setParameter('country', $searchdata->country);
        }
        return $query->getQuery();
    }

    /**
     * Return a query used for the paginator bundle
     * @return Query
     */
    public function findAllAvailableQuery(): Query
    {
        return $this->findAvailableQuery(true)
            ->getQuery();
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
