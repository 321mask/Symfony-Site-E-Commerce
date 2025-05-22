<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\SearchFilters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    //    /**
    //     * @return Products[] Returns an array of Products objects
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

    //    public function findOneBySomeField($value): ?Products
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function myFindAll() {
        $queryBuilder = $this->createQueryBuilder('p');
        $query = $queryBuilder->getQuery();
        $results = $query->getResult();
        return $results;
    }
    public function myFind($value): array {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->where('p.id = :val')->setParameter('val', $value);
        $query = $queryBuilder->getQuery();
        $results = $query->getResult();
        return $results;
    }
    public function myBetweenVal($minValue, $maxValue): array {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->where('p.price BETWEEN :minVal AND :maxVal')->setParameter('minVal', $minValue)->setParameter('maxVal', $maxValue);
        $query = $queryBuilder->getQuery();
        $results = $query->getResult();
        return $results;
    }
    public function findOrdersFromUser($user): array {
        return $this->createQueryBuilder('o')
            ->where('o.user = :userId') 
            ->setParameter('user', $user) 
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findWithSearch(SearchFilters $search): array {
        $queryBuilder = $this->createQueryBuilder('p');

        if (count($search->getCategories()) > 0) {
            $queryBuilder->andWhere('p.category IN (:categories)')
                ->setParameter('categories', $search->getCategories());
        }

        if ($search->getString() && $search->getString() !== '') {
            $keywords = explode(' ', $search->getString());
            $orConditions = [];
            $params = [];
            
            
            foreach ($keywords as $index => $keyword) {
                $orConditions[] = '(p.name LIKE :q' . $index . ' OR p.subtitle LIKE :q' . $index . ')';
                $params['q' . $index] = '%' . $keyword . '%';
            }
    
            
            $queryBuilder->andWhere(implode(' OR ', $orConditions));
            
          
            foreach ($params as $key => $value) {
                $queryBuilder->setParameter($key, $value);
            }
        }
        return $queryBuilder->getQuery()->getResult();
    }
}
