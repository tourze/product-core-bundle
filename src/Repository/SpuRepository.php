<?php

namespace Tourze\ProductCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductServiceContracts\SpuLoaderInterface;

/**
 * @extends ServiceEntityRepository<Spu>
 */
#[AsRepository(entityClass: Spu::class)]
#[AsAlias(id: SpuLoaderInterface::class)]
final class SpuRepository extends ServiceEntityRepository implements SpuLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Spu::class);
    }

    public function save(Spu $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Spu $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 批量保存
     *
     * @param array<Spu> $entities
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

    public function loadSpuByIdentifier(string $identifier): ?\Tourze\ProductServiceContracts\SPU
    {
        return $this->findOneBy(['gtin' => $identifier]);
    }

    public function createSpu(
        ?string $gtin = null,
        ?string $title = null,
        ?string $remark = null,
        ?bool $valid = true,
    ): \Tourze\ProductServiceContracts\SPU {
        $spu = new Spu();
        $spu->setGtin($gtin);
        $spu->setTitle($title ?? '');
        $spu->setRemark($remark);
        $spu->setValid($valid);

        return $spu;
    }

    public function loadOrCreateSpu(
        ?string $gtin = null,
        ?string $title = null,
        ?string $remark = null,
        ?bool $valid = true,
    ): \Tourze\ProductServiceContracts\SPU {
        if (null !== $gtin) {
            $existing = $this->loadSpuByIdentifier($gtin);
            if (null !== $existing) {
                return $existing;
            }
        }

        $spu = $this->createSpu($gtin, $title, $remark, $valid);
        assert($spu instanceof Spu);
        $this->save($spu);

        return $spu;
    }

    public function findByGtin(string $gtin, ?int $excludeId = null): ?Spu
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

        /** @var Spu|null */
        return $qb->getQuery()->getOneOrNullResult();
    }
}
