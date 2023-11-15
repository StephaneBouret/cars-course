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

    public function countItems(SearchData $search): int
    {
        $query = $this->getSearchQuery($search)->getQuery();
        return count($query->getResult());
    }

    /**
     * Récupère le prix minimum et maximum correspondant à une recherche
     *
     * @param SearchData $search
     * @return integer[]
     */
    public function findMinMaxPrice(SearchData $search): array
    {
        $results = $this->getSearchQuery($search, true, false, false)
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
        $results = $this->getSearchQuery($search, false, true, false)
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
        $results = $this->getSearchQuery($search, false, false, true)
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

        if (!empty($search->minPrice) && $ignorePrice === false && $ignoreKms === false && $ignoreDate === false) {
            $min = ($search->minPrice) * 100;
            $query = $query
                ->andWhere('p.price >= :min')
                ->setParameter('min', $min);
        }

        if (!empty($search->maxPrice) && $ignorePrice === false && $ignoreKms === false && $ignoreDate === false) {
            $max = ($search->maxPrice) * 100;
            $query = $query
                ->andWhere('p.price <= :max')
                ->setParameter('max', $max);
        }

        if (!empty($search->minKms) && $ignorePrice === false && $ignoreKms === false && $ignoreDate === false) {
            $minKms = ($search->minKms);
            $query = $query
                ->andWhere('p.kilometers >= :minKms')
                ->setParameter('minKms', $minKms);
        }

        if (!empty($search->maxKms) && $ignorePrice === false && $ignoreKms === false && $ignoreDate === false) {
            $maxKms = ($search->maxKms);
            $query = $query
                ->andWhere('p.kilometers <= :maxKms')
                ->setParameter('maxKms', $maxKms);
        }

        if (!empty($search->minCirculationAt) && $ignorePrice === false && $ignoreKms === false && $ignoreDate === false) {
            $minDate = ($search->minCirculationAt);
            dump($minDate);
            $query = $query
                ->andWhere('YEAR(p.circulationAt) >= YEAR(:minDate)')
                ->setParameter('minDate', $minDate);
        }

        if (!empty($search->maxCirculationAt) && $ignorePrice === false && $ignoreKms === false && $ignoreDate === false) {
            $maxDate = ($search->maxCirculationAt);
            dump($maxDate);
            $query = $query
                ->andWhere('YEAR(p.circulationAt) <= YEAR(:maxDate)')
                ->setParameter('maxDate', $maxDate);
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
