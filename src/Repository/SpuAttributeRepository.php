<?php

namespace Tourze\ProductCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\ProductCoreBundle\Entity\SpuAttribute;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method SpuAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpuAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpuAttribute[]    findAll()
 * @method SpuAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpuAttributeRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpuAttribute::class);
    }
}
