<?php

namespace ProductBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProductBundle\Entity\SkuAttribute;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method SkuAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method SkuAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method SkuAttribute[]    findAll()
 * @method SkuAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkuAttributeRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SkuAttribute::class);
    }
}
