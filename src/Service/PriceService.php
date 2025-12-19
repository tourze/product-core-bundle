<?php

namespace Tourze\ProductCoreBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Enum\PriceType;

/**
 * 价格服务
 */
#[Autoconfigure(public: true)]
final class PriceService
{
    /**
     * 获取SKU价格（根据类型）
     */
    public function getSkuPrice(Sku $sku, PriceType $type): ?string
    {
        return match ($type) {
            PriceType::SALE => $sku->getMarketPrice(),
            PriceType::COST => $sku->getCostPrice(),
            PriceType::ORIGINAL_PRICE => $sku->getOriginalPrice(),
            default => null,
        };
    }

    /**
     * 获取销售价格
     */
    public function getSalePrice(Sku $sku): ?string
    {
        return $sku->getMarketPrice();
    }

    /**
     * 获取成本价
     */
    public function getCostPrice(Sku $sku): ?string
    {
        return $sku->getCostPrice();
    }

    /**
     * 获取原价
     */
    public function getOriginalPrice(Sku $sku): ?string
    {
        return $sku->getOriginalPrice();
    }

    /**
     * 获取积分价格
     */
    public function getIntegralPrice(Sku $sku): ?int
    {
        return $sku->getIntegralPrice();
    }

    /**
     * 获取币种
     */
    public function getCurrency(Sku $sku): string
    {
        return $sku->getCurrency();
    }

    /**
     * 获取税率
     */
    public function getTaxRate(Sku $sku): ?float
    {
        return $sku->getTaxRate();
    }
}
