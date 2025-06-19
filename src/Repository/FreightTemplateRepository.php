<?php

namespace ProductBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProductBundle\Entity\FreightTemplate;
use Tourze\TrainCourseBundle\Trait\CommonRepositoryAware;

/**
 * @method FreightTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method FreightTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method FreightTemplate[]    findAll()
 * @method FreightTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FreightTemplateRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FreightTemplate::class);
    }
}
