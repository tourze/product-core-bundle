<?php

namespace ProductBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use ProductBundle\Entity\SpuAttribute;

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
