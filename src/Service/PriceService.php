<?php

namespace ProductBundle\Service;

use BizUserBundle\Entity\BizUser;
use Carbon\CarbonInterface;
use ProductBundle\Entity\Price;
use ProductBundle\Entity\Sku;
use ProductBundle\Enum\PriceType;

/**
 * 价格服务
 */
class PriceService
{
    public function getFinalPrice(Sku $sku, PriceType $type): ?Price
    {
        $result = null;

        /** @var Price $price */
        foreach ($sku->getSortedPrices() as $price) {
            if ($price->getType() === $type) {
                if (null === $result) {
                    $result = $price;
                    continue;
                }

                // 取最低的价格
                if ($price->getPrice() < $result->getPrice()) {
                    $result = $price;
                }
            }
        }

        return $result;
    }

    /**
     * 获取销售价格
     */
    public function getSalePrices(BizUser $user, Sku $sku, CarbonInterface $time): array
    {
        $result = [];

        $skuPrices = $sku->determineOnTimeSalePrice($time); // 计算当前这个时刻的价格
        foreach ($skuPrices as $skuPrice) {
            if (!$skuPrice->getPrice()) {
                continue;
            }
            if (PriceType::SALE !== $skuPrice->getType()) {
                continue;
            }

            // 取价格最低的那个
            if (!isset($result[$skuPrice->getCurrency()])) {
                $result[$skuPrice->getCurrency()] = $skuPrice;
            }
            if ($result[$skuPrice->getCurrency()]->getPrice() > $skuPrice->getPrice()) {
                $result[$skuPrice->getCurrency()] = $skuPrice;
            }
        }

        return $result;
    }
}
