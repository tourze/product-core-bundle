<?php

namespace Tourze\ProductCoreBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\ProductAttributeBundle\Entity\SkuAttribute;
use Tourze\ProductAttributeBundle\Entity\SpuAttribute;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;

/**
 * 产品数据格式化服务
 * 负责将产品实体转换为各种数组格式
 */
#[Autoconfigure(public: true)]
final class ProductArrayFormatterService
{
    /**
     * 将SPU转换为数组格式
     *
     * @return array<string, mixed>
     */
    public function formatSpuArray(Spu $spu): array
    {
        $skus = [];
        foreach ($spu->getSkus() as $sku) {
            $skus[] = $this->formatSkuForSpuArray($sku);
        }

        $attributes = [];
        foreach ($spu->getAttributes() as $attribute) {
            $attributes[] = $this->formatSpuAttributeArray($attribute);
        }

        return [
            'id' => $spu->getId(),
            'supplier' => null,
            'gtin' => $spu->getGtin(),
            'title' => $spu->getTitle(),
            'subtitle' => $spu->getSubtitle(),
            'type' => $spu->getType(),
            'skus' => $skus,
            'mainPic' => $spu->getMainPic(),
            'thumbs' => $spu->getThumbs(),
            'attributes' => $attributes,
            'content' => $spu->getContent(),
            'mainThumb' => $this->getSpuMainThumb($spu),
        ];
    }

    /**
     * 将SPU转换为结账数组格式
     *
     * @return array<string, mixed>
     */
    public function formatSpuCheckoutArray(Spu $spu): array
    {
        return [
            'id' => $spu->getId(),
            'supplier' => null,
            'gtin' => $spu->getGtin(),
            'title' => $spu->getTitle(),
            'subtitle' => $spu->getSubtitle(),
            'type' => $spu->getType(),
            'mainPic' => $spu->getMainPic(),
            'thumbs' => $spu->getThumbs(),
            'content' => $spu->getContent(),
            'mainThumb' => $this->getSpuMainThumb($spu),
        ];
    }

    /**
     * 将SKU转换为SPU数组中的格式
     *
     * @return array<string, mixed>
     */
    public function formatSkuForSpuArray(Sku $sku): array
    {
        return [
            'id' => $sku->getId(),
            'gtin' => $sku->getGtin(),
            'unit' => $sku->getUnit(),
            'needConsignee' => $sku->isNeedConsignee(),
            'thumbs' => $sku->getThumbs(),
            'attributes' => $this->formatSkuAttributesArray($sku, 'retrieveSpuArray'),
            'valid' => $sku->isValid(),
            'displayAttribute' => $this->getSkuDisplayAttribute($sku),
            'marketPrice' => $sku->getMarketPrice(),
            'costPrice' => $sku->getCostPrice(),
            'originalPrice' => $sku->getOriginalPrice(),
            'mainThumb' => $this->getSkuMainThumb($sku),
            'salesCount' => $this->getSkuSalesCount($sku),
        ];
    }

    /**
     * 将SKU转换为常规数组格式
     *
     * @return array<string, mixed>
     */
    public function formatSkuArray(Sku $sku): array
    {
        return [
            'id' => $sku->getId(),
            'mainThumb' => $this->getSkuMainThumb($sku),
            'thumbs' => $sku->getThumbs(),
            'title' => $sku->getTitle(),
            'unit' => $sku->getUnit(),
            'gtin' => $sku->getGtin(),
            'valid' => $sku->isValid(),
            'attributes' => $this->formatSkuAttributesArray($sku, 'retrieveSkuArray'),
            'displayAttribute' => $this->getSkuDisplayAttribute($sku),
            'salesCount' => $this->getSkuSalesCount($sku),
            'marketPrice' => $sku->getMarketPrice(),
            'costPrice' => $sku->getCostPrice(),
            'originalPrice' => $sku->getOriginalPrice(),
        ];
    }

    /**
     * 将SKU转换为结账数组格式
     *
     * @return array<string, mixed>
     */
    public function formatSkuCheckoutArray(Sku $sku): array
    {
        return [
            'id' => $sku->getId(),
            'gtin' => $sku->getGtin(),
            'unit' => $sku->getUnit(),
            'thumbs' => $sku->getThumbs(),
            'attributes' => $this->formatSkuAttributesArray($sku, 'retrieveSpuArray'),
            'valid' => $sku->isValid(),
            'displayAttribute' => $this->getSkuDisplayAttribute($sku),
            'mainThumb' => $this->getSkuMainThumb($sku),
            'salesCount' => $this->getSkuSalesCount($sku),
        ];
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
     * 获取SPU主缩略图
     */
    public function getSpuMainThumb(Spu $spu): string
    {
        $mainPic = $spu->getMainPic();
        if (null !== $mainPic && '' !== $mainPic) {
            return $mainPic;
        }

        $thumbs = $spu->getThumbs();
        if (null === $thumbs || [] === $thumbs) {
            return '';
        }

        $firstThumb = $thumbs[0] ?? null;
        if (is_array($firstThumb) && isset($firstThumb['url'])) {
            $url = $firstThumb['url'];

            return is_string($url) ? $url : (is_scalar($url) ? (string) $url : '');
        }

        return '';
    }

    /**
     * 获取SKU主缩略图
     */
    private function getSkuMainThumb(Sku $sku): string
    {
        $thumbs = $sku->getThumbs();
        if (null === $thumbs || [] === $thumbs) {
            $spu = $sku->getSpu();

            return null !== $spu ? $this->getSpuMainThumb($spu) : '';
        }

        $firstThumb = $thumbs[0] ?? null;
        if (is_array($firstThumb) && isset($firstThumb['url'])) {
            $url = $firstThumb['url'];

            return is_string($url) ? $url : (is_scalar($url) ? (string) $url : '');
        }

        return '';
    }

    /**
     * 获取SKU显示属性
     */
    private function getSkuDisplayAttribute(Sku $sku): string
    {
        $res = [];
        foreach ($sku->getAttributes() as $attribute) {
            if (in_array($attribute->getName(), ['itemCode', 'itemID', 'itemTitle', 'shopNick', 'skuTitle', 'storeID'], true)) {
                continue;
            }

            $res[] = "{$attribute->getName()}{$attribute->getValue()}";
        }

        if ([] === $res) {
            return (string) $sku->getGtin();
        }

        return implode('+', $res);
    }

    /**
     * 获取SKU销量总计
     */
    private function getSkuSalesCount(Sku $sku): int
    {
        return $sku->getSalesReal() + $sku->getSalesVirtual();
    }

    /**
     * 格式化SKU属性数组
     *
     * @param Sku $sku
     * @param string $method
     * @return array
     */
    private function formatSkuAttributesArray(Sku $sku, string $method = 'retrieveSkuArray'): array
    {
        $attributes = [];
        foreach ($sku->getAttributes() as $attribute) {
            $attributes[] = match ($method) {
                'retrieveSpuArray' => $this->formatSkuAttributeArray($attribute, 'spu'),
                'retrieveAdminArray' => $this->formatSkuAttributeArray($attribute, 'admin'),
                default => $this->formatSkuAttributeArray($attribute, 'sku'),
            };
        }

        return $attributes;
    }

    /**
     * 格式化SKU属性数组
     *
     * @return array<string, mixed>
     */
    private function formatSkuAttributeArray(SkuAttribute $attribute, string $context): array
    {
        $baseArray = [
            'id' => $attribute->getId(),
            'name' => $attribute->getName(),
            'value' => $attribute->getValue(),
        ];

        return match ($context) {
            'sku' => array_merge($baseArray, [
                'remark' => $attribute->getRemark(),
                'allowCustomized' => $attribute->isAllowCustomized(),
            ]),
            'admin' => array_merge($baseArray, [
                'createTime' => $attribute->getCreateTime()?->format('Y-m-d H:i:s'),
                'updateTime' => $attribute->getUpdateTime()?->format('Y-m-d H:i:s'),
            ]),
            default => $baseArray,
        };
    }

    /**
     * 格式化SPU属性数组
     *
     * @return array<string, mixed>
     */
    private function formatSpuAttributeArray(SpuAttribute $attribute): array
    {
        return [
            'id' => $attribute->getId(),
            'name' => $attribute->getName(),
            'value' => $attribute->getValue(),
        ];
    }
}
