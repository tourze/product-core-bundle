<?php

namespace ProductCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProductCoreBundle\Entity\SpuSubscribe;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method SpuSubscribe|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpuSubscribe|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpuSubscribe[]    findAll()
 * @method SpuSubscribe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpuSubscribeRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpuSubscribe::class);
    }
}
