<?php

namespace Tourze\ProductCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method Spu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Spu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Spu[]    findAll()
 * @method Spu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpuRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Spu::class);
    }
}
