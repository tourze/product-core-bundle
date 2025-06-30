<?php

namespace Tourze\ProductCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\ProductCoreBundle\Entity\SpuLimitRule;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method SpuLimitRule|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpuLimitRule|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpuLimitRule[]    findAll()
 * @method SpuLimitRule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpuLimitRuleRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpuLimitRule::class);
    }
}
