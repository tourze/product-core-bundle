<?php

namespace ProductCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProductCoreBundle\Entity\SkuPackage;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method SkuPackage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SkuPackage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SkuPackage[]    findAll()
 * @method SkuPackage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkuPackageRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SkuPackage::class);
    }
}
