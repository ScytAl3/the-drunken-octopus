<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\Query;
use App\Data\SearchData;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * 
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }

    /**
     * Return a pagination with the products based on the search.
     * @param SearchData $searchdata
     * 
     * @return PaginationInterface 
     */
    public function findSearch(SearchData $searchdata): PaginationInterface
    {
        // Construction de la requête
        $query = $this
            ->createQueryBuilder('p')
            ->addSelect('s')            // Ajout de la table Style
            ->join('p.style', 's')
            ->addSelect('c')            // Ajout de la table Country
            ->join('p.country', 'c')
            ->addSelect('b')            // Ajout de la table Brewery
            ->join('p.brewery', 'b');
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
        // Contrôle du filtre sur les brasserie
        if (!empty($searchdata->brewery)) {
            $query = $query
                ->andWhere('b.id IN (:brewery)')
                ->setParameter('brewery', $searchdata->brewery);
        }

        $query = $query->getQuery();

        $pagination = $this->paginator->paginate(
            $query,             /* query NOT result */
            $searchdata->page,  /* page number*/
            12                  /* limit per page*/
        );
        // Pour connaître le nombre total d'items, sinon le max = la limite par page
        $pagination->getTotalItemCount();
        // Mise en page du template de la pagination
        $pagination->setCustomParameters([
            'align' => 'center',    // center|right (for template: twitter_bootstrap_v4_pagination)
            'size' => 'small',      // small|large (for template: twitter_bootstrap_v4_pagination)
            'style' => 'bottom',
            'span_class' => 'whatever',
        ]);

        return $pagination;
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
