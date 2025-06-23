<?php

namespace ProductCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProductCoreBundle\Entity\SpuDescriptionAttribute;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method SpuDescriptionAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpuDescriptionAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpuDescriptionAttribute[]    findAll()
 * @method SpuDescriptionAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpuDescriptionAttributeRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpuDescriptionAttribute::class);
    }
}
