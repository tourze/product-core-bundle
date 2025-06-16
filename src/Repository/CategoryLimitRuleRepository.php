<?php

namespace ProductBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use ProductBundle\Entity\CategoryLimitRule;

/**
 * @method CategoryLimitRule|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryLimitRule|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryLimitRule[]    findAll()
 * @method CategoryLimitRule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryLimitRuleRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryLimitRule::class);
    }
}
