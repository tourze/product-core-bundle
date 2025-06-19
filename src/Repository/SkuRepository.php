<?php

namespace ProductBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProductBundle\Entity\Sku;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method Sku|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sku|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sku[]    findAll()
 * @method Sku[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkuRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sku::class);
    }
}
