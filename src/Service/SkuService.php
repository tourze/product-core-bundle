<?php

namespace Tourze\ProductCoreBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Repository\SkuRepository;

/**
 * SKU 服务
 */
#[Autoconfigure(public: true)]
final readonly class SkuService implements SkuServiceInterface
{
    public function __construct(
        private SkuRepository $skuRepository,
    ) {
    }

    /**
     * 获取所有SKU
     *
     * @return Sku[]
     */
    public function getAllSkus(): array
    {
        return $this->skuRepository->findAll();
    }

    /**
     * 根据ID查找SKU
     */
    public function findById(string $id): ?Sku
    {
        return $this->skuRepository->find($id);
    }

    /**
     * 增加SKU的实际销量
     *
     * 注意：此方法使用数据库原子更新操作，不考虑并发
     * 数据库层面的 UPDATE 语句是原子的，可以安全处理并发情况
     */
    public function increaseSalesReal(string $skuId, int $quantity): void
    {
        $this->skuRepository->createQueryBuilder('a')
            ->update()
            ->set('a.salesReal', "a.salesReal + {$quantity}")
            ->where('a.id = :id')
            ->setParameter('id', $skuId)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * 根据ID列表批量查找SKU
     *
     * @param string[] $ids
     * @return Sku[]
     */
    public function findByIds(array $ids): array
    {
        if ([] === $ids) {
            return [];
        }

        return $this->skuRepository->findBy(['id' => $ids]);
    }

    public function findByGtin(string $gtin): ?Sku
    {
        return $this->skuRepository->findOneBy(['gtin' => $gtin]);
    }

    public function findByGtins(array $gtins): array
    {
        if ([] === $gtins) {
            return [];
        }

        return $this->skuRepository->findBy(['gtin' => $gtins]);
    }
}
