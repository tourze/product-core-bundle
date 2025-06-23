<?php

namespace ProductCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProductCoreBundle\Entity\SpuRelation;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method SpuRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpuRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpuRelation[]    findAll()
 * @method SpuRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpuRelationRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpuRelation::class);
    }
}
