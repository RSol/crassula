<?php

namespace App\Repository;

use App\Entity\Rate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Rate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rate[]    findAll()
 * @method Rate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateRepository extends ServiceEntityRepository
{
    /**
     * RateRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    /**
     * @param $source
     * @param $from_currency
     * @param $to_currency
     * @return float
     */
    public function findRate($source, $from_currency, $to_currency): float
    {
        if ($rate = $this->getRate([
            'source' => $source,
            'from_currency' => $from_currency,
            'to_currency' => $to_currency,
        ])) {
            return $rate->getRate();
        }

        if ($rate = $this->getRate([
            'source' => $source,
            'from_currency' => $to_currency,
            'to_currency' => $from_currency,
        ])) {
            return 1 / $rate->getRate();
        }

        $rate_from = $this->getRate(Rate::getParamsBySource($source, $from_currency));
        $rate_to = $this->getRate(Rate::getParamsBySource($source, $to_currency));

        if ($rate_from && $rate_to) {
            return $rate_to->getRate() / $rate_from->getRate();
        }

        return 0;
    }

    /**
     * @param $params
     * @return Rate|null
     * @throws NonUniqueResultException
     */
    private function getRate($params): ?Rate
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.from_currency = :from_currency AND r.to_currency = :to_currency AND r.source = :source')
            ->setParameters($params)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
