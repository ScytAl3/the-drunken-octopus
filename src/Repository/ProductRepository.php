<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\Query;
use App\Data\SearchData;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use InvalidArgumentException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use RuntimeException;

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
     * Retourne une pagination avec les produits en fonction de la recherche
     * @param SearchData $searchData
     * 
     * @return PaginationInterface 
     */
    public function findSearch(SearchData $searchData): PaginationInterface
    {    
        // Appelle de la methode privée qui construit la requête
        $query = $this->getSearchQuery($searchData);

        $pagination = $this->paginator->paginate(
            $query,             /* query NOT result */
            $searchData->page,  /* page number*/
            12,                  /* limit per page*/
            [
                'defaultSortFieldName'      => 'p.createdAt',
                'defaultSortDirection' => 'DESC'
            ]
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
     * Retourne une requête utilisée par paginator bundle
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
     * retourne la requête construite en fonction du booleen
     * 
     * @return QueryBuilder
     */
    private function findAvailableQuery($boolean): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.availability = :val')
            ->setParameter('val', $boolean);
    }

    /**
     * Retourne la requête construite en fonction du filtre
     * @param SearchData $searchData
     * 
     * @return QueryBuilder 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     */
    private function getSearchQuery(SearchData $searchData): QueryBuilder
    {
        // Construction de la requête
        $query = $this
            ->createQueryBuilder('p')
            ->addSelect('s')                        // Ajout de la table Style
            ->join('p.style', 's')
            ->addSelect('c')                        // Ajout de la table Country
            ->join('p.country', 'c')
            ->addSelect('b')                        // Ajout de la table Brewery
            ->join('p.brewery', 'b')
            ->andWhere('p.availability = true');    // Uniquement les produits toujours en vente
        // Contrôle du champ de recherche
        if (!empty($searchData->q)) {
            $query = $query
                ->andWhere('p.title LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }
        // Contrôle du filtre sur les styles
        if (!empty($searchData->style)) {
            $query = $query
                ->andWhere('s.id IN (:style)')
                ->setParameter('style', $searchData->style);
        }
        // Contrôle du filtre sur les pays
        if (!empty($searchData->country)) {
            $query = $query
                ->andWhere('c.id IN (:country)')
                ->setParameter('country', $searchData->country);
        }
        // Contrôle du filtre sur les brasserie
        if (!empty($searchData->brewery)) {
            $query = $query
                ->andWhere('b.id IN (:brewery)')
                ->setParameter('brewery', $searchData->brewery);
        }
        return $query;
    }
}
