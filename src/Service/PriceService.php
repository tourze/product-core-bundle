<?php

namespace Tourze\ProductCoreBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\ProductCoreBundle\Entity\Price;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Enum\PriceType;
use Tourze\ProductCoreBundle\Repository\PriceRepository;

/**
 * 价格服务
 */
#[Autoconfigure(public: true)]
final class PriceService
{
    public function __construct(
        private readonly PriceRepository $priceRepository,
    ) {
    }

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
     * 根据运费ID和SKU列表查找运费价格
     *
     * @param array<Sku> $skus
     */
    public function findFreightPriceBySkus(string $freightId, array $skus): ?Price
    {
        return $this->priceRepository->findOneBy([
            'id' => $freightId,
            'type' => PriceType::FREIGHT,
            'sku' => array_values($skus),
        ]);
    }

    /**
     * 根据ID查找价格
     */
    public function findPriceById(string $priceId): ?Price
    {
        return $this->priceRepository->find($priceId);
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
     * 获取SKU的所有价格记录
     *
     * @return array<Price>
     */
    public function getPricesBySku(Sku $sku): array
    {
        return $this->priceRepository->findBy(['sku' => $sku]);
    }
}
