<?php

namespace App\Repository;

use App\Entity\Product;
use App\Data\SearchData;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var $paginator
     */
    protected $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }

    /**
     * Récupère les produits en lien avec une recherche
     *
     * @return PaginationInterface
     */
    public function findSearch(SearchData $search): PaginationInterface
    {
        $query = $this->getSearchQuery($search)->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            6
        );
    }

    /**
     * Récupère le prix minimum et maximum correspondant à une recherche
     *
     * @param SearchData $search
     * @return integer[]
     */
    public function findMinMaxPrice(SearchData $search): array
    {
        $results = $this->getSearchQuery($search, true, true, true)
            ->select('MIN(p.price) as minPrice', 'MAX(p.price) as maxPrice')
            ->getQuery()
            ->getScalarResult();
        return [(int)$results[0]['minPrice'], (int)$results[0]['maxPrice']];
    }

    /**
     * Récupère les kilomètres minimum et maximum correspondant à une recherche
     *
     * @param SearchData $search
     * @return integer[]
     */
    public function findMinMaxKms(SearchData $search): array
    {
        $results = $this->getSearchQuery($search, true, true, true)
            ->select('MIN(p.kilometers) as minKms', 'MAX(p.kilometers) as maxKms')
            ->getQuery()
            ->getScalarResult();
        return [(int)$results[0]['minKms'], (int)$results[0]['maxKms']];
    }

    /**
     * Récupère les années minimum et maximum correspondant à une recherche
     *
     * @param SearchData $search
     * @return integer[]
     */
    public function findMinMaxDate(SearchData $search): array
    {
        $results = $this->getSearchQuery($search, true, true, true)
            ->select('MIN(p.circulationAt) as minDate', 'MAX(p.circulationAt) as maxDate')
            ->getQuery()
            ->getResult();
        return [$results[0]['minDate'], $results[0]['maxDate']];
    }

    public function getSearchQuery(SearchData $search, $ignorePrice = false, $ignoreKms = false, $ignoreDate = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->select('m', 'p')
            ->join('p.category', 'c')
            ->join('p.model', 'm');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->minPrice) && $ignorePrice === false) {
            $min = ($search->minPrice) * 100;
            $query = $query
                ->andWhere('p.price >= :min')
                ->setParameter('min', $min);
        }

        if (!empty($search->maxPrice) && $ignorePrice === false) {
            $max = ($search->maxPrice) * 100;
            $query = $query
                ->andWhere('p.price <= :max')
                ->setParameter('max', $max);
        }

        if (!empty($search->minKms) && $ignoreKms === false) {
            $min = ($search->minKms);
            $query = $query
                ->andWhere('p.kilometers >= :min')
                ->setParameter('min', $min);
        }

        if (!empty($search->maxKms) && $ignoreKms === false) {
            $max = ($search->maxKms);
            $query = $query
                ->andWhere('p.kilometers <= :max')
                ->setParameter('max', $max);
        }

        if (!empty($search->minCirculationAt) && $ignoreDate === false) {
            $min = ($search->minCirculationAt);
            $query = $query
                ->andWhere('p.circulationAt >= :min')
                ->setParameter('min', $min);
        }

        if (!empty($search->maxCirculationAt) && $ignoreDate === false) {
            $max = ($search->maxCirculationAt);
            $query = $query
                ->andWhere('p.circulationAt <= :max')
                ->setParameter('max', $max->modify('+1 year'));
        }

        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }

        if (!empty($search->model)) {
            $query = $query
                ->andWhere('m.id IN (:model)')
                ->setParameter('model', $search->model);
        }

        $query->orderBy('p.name', 'ASC');

        return $query;
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
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

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
