<?php

namespace Tourze\ProductCoreBundle\Service;

use Tourze\ProductCoreBundle\Entity\Sku;

/**
 * SKU服务接口
 */
interface SkuServiceInterface
{
    /**
     * 获取所有SKU
     *
     * @return Sku[]
     */
    public function getAllSkus(): array;

    /**
     * 根据ID查找SKU
     */
    public function findById(string $id): ?Sku;

    /**
     * 增加SKU的实际销量
     */
    public function increaseSalesReal(string $skuId, int $quantity): void;

    /**
     * 根据ID列表批量查找SKU
     *
     * @param string[] $ids
     * @return Sku[]
     */
    public function findByIds(array $ids): array;
}
