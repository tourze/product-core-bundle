<?php

namespace Tourze\ProductCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductServiceContracts\SkuLoaderInterface;
use Tourze\ProductServiceContracts\SPU;

/**
 * @extends ServiceEntityRepository<Sku>
 */
#[AsRepository(entityClass: Sku::class)]
#[AsAlias(id: SkuLoaderInterface::class)]
final class SkuRepository extends ServiceEntityRepository implements SkuLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sku::class);
    }

    public function save(Sku $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sku $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 批量保存
     *
     * @param array<Sku> $entities
     */
    public function saveAll(array $entities, bool $flush = true): void
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->persist($entity);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 刷新实体管理器
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * 清空实体管理器
     */
    public function clear(): void
    {
        $this->getEntityManager()->clear();
    }

    public function loadSkuByIdentifier(string $identifier): ?\Tourze\ProductServiceContracts\SKU
    {
        return $this->findOneBy(['gtin' => $identifier, 'valid' => true])
            ?? $this->findOneBy(['id' => $identifier, 'valid' => true]);
    }

    public function createSku(
        SPU $spu,
        ?string $gtin = null,
        ?string $mpn = null,
        ?string $remark = null,
        ?bool $valid = true,
    ): \Tourze\ProductServiceContracts\SKU {
        assert($spu instanceof \Tourze\ProductCoreBundle\Entity\Spu);

        $sku = new Sku();

        $sku->setSpu($spu);
        $sku->setGtin($gtin);
        $sku->setMpn($mpn);
        $sku->setRemark($remark);
        $sku->setValid($valid);
        $sku->setUnit('个');

        $this->save($sku);

        return $sku;
    }

    /**
     * 根据GTIN查找SKU
     */
    public function findByGtin(string $gtin, ?int $excludeId = null): ?Sku
    {
        if ('' === $gtin) {
            return null;
        }

        $qb = $this->createQueryBuilder('s')
            ->where('s.gtin = :gtin')
            ->setParameter('gtin', $gtin)
        ;

        if (null !== $excludeId) {
            $qb->andWhere('s.id != :excludeId')
                ->setParameter('excludeId', $excludeId)
            ;
        }

        /** @var Sku|null */
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * 获取所有有效的 SKU
     *
     * @return array<Sku>
     */
    public function findAllValid(): array
    {
        /** @var array<Sku> */
        return $this->createQueryBuilder('s')
            ->leftJoin('s.spu', 'spu')
            ->where('s.valid = :valid')
            ->setParameter('valid', true)
            ->orderBy('spu.title', 'ASC')
            ->addOrderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
