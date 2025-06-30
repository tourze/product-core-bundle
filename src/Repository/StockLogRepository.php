<?php

namespace Tourze\ProductCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\ProductCoreBundle\Entity\StockLog;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method StockLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockLog[]    findAll()
 * @method StockLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockLogRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockLog::class);
    }
}
