<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use App\Entity\PurchaseProduct;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use RuntimeException;

/**
 * @method PurchaseProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseProduct[]    findAll()
 * @method PurchaseProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseProduct::class);
    }

    /**
     * Retourne la liste des produits associés à cette commande
     * @param int $orderId 
     * @return PurchaseProduct[] 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     * @throws ORMException 
     */
    public function findPurchasedProducts(Int $orderId): array
    {
        return $this->getPurchasedProductQuery($orderId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la requête construite
     * @param int $id 
     * @return QueryBuilder 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     */
    private function getPurchasedProductQuery(Int $id): QueryBuilder
    {
        // Construction de la requête
        $query = $this
            ->createQueryBuilder('p')
            ->addSelect('o')            // Jointure sur la table orders
            ->join('p.purchaseOrder', 'o')
            ->addSelect('i')            // Jointure sur la table products
            ->join('p.product', 'i')
            ->where('p.purchaseOrder = :val')
            ->setParameter('val', $id);

        return $query;
    }
}
