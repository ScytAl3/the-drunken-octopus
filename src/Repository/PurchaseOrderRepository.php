<?php

namespace App\Repository;

use App\Entity\PurchaseOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use RuntimeException;

/**
 * @method PurchaseOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseOrder[]    findAll()
 * @method PurchaseOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseOrder::class);
    }

    /**
     * Retourne la liste des commandes de l'utilisateur connecté
     * @param int $userId 
     * @return PurchaseOrder[] 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     * @throws ORMException 
     */
    public function findOrderHistory(Int $userId): array
    {
        return $this->getOrderHistoryQuery($userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la requête construite
     * @param int $id 
     * 
     * @return QueryBuilder 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     */
    private function getOrderHistoryQuery(Int $id): QueryBuilder
    {
        // Construction de la requête
        $query = $this
            ->createQueryBuilder('p')
            ->addSelect('u')            // Jointure sur la table Users
            ->join('p.user', 'u')
            ->addSelect('l')            // Jointure sur la table purchase_products
            ->join('p.purchaseProducts', 'l')
            ->addSelect('i')            // Jointure sur la table products
            ->join('l.product', 'i')
            ->where('u.id = :val')
            ->setParameter('val', $id);
        
        return $query;
    }
}
