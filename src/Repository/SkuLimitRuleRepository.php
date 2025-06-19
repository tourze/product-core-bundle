<?php

namespace ProductBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProductBundle\Entity\SkuLimitRule;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method SkuLimitRule|null find($id, $lockMode = null, $lockVersion = null)
 * @method SkuLimitRule|null findOneBy(array $criteria, array $orderBy = null)
 * @method SkuLimitRule[]    findAll()
 * @method SkuLimitRule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkuLimitRuleRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SkuLimitRule::class);
    }
}
