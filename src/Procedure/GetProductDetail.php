<?php

namespace Tourze\ProductCoreBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Exception\ProductNotFoundException;
use Tourze\ProductCoreBundle\Exception\ProductStatusException;
use Tourze\ProductCoreBundle\Repository\SpuRepository;
use Tourze\ProductCoreBundle\Service\ProductArrayFormatterService;

#[MethodTag(name: '产品模块')]
#[MethodDoc(summary: '获取商品详情')]
#[MethodExpose(method: 'GetProductDetail')]
final class GetProductDetail extends BaseProcedure
{
    #[MethodParam(description: '商品ID')]
    public int $id = 0;

    public function __construct(
        private readonly SpuRepository $spuRepository,
        private readonly ProductArrayFormatterService $formatterService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        if ($this->id <= 0) {
            throw new ProductNotFoundException('商品ID不能为空');
        }

        $spu = $this->spuRepository->find($this->id);

        if (null === $spu) {
            throw new ProductNotFoundException('商品不存在');
        }

        if (true !== $spu->isValid()) {
            throw new ProductStatusException('商品已下架或未审核');
        }

        return $this->formatProductDetail($spu);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatProductDetail(Spu $spu): array
    {
        $skus = [];
        foreach ($spu->getSkus() as $sku) {
            if (true !== $sku->isValid()) {
                continue;
            }

            $skuData = [
                'id' => $sku->getId(),
                'gtin' => $sku->getGtin(),
                'title' => $sku->getFullName(),
                'valid' => $sku->isValid(),
                'stock' => 0,
                'salesReal' => $sku->getSalesReal(),
                'salesVirtual' => $sku->getSalesVirtual(),
                'totalSales' => $sku->getSalesReal() + $sku->getSalesVirtual(),
                'prices' => [],
                'attributes' => [],
                'mainThumb' => null !== $sku->getThumbs() && [] !== $sku->getThumbs() ? $sku->getThumbs()[0] : '',
                'thumbs' => $sku->getThumbs(),
            ];

            // 添加新的价格字段
            $skuData['marketPrice'] = $sku->getMarketPrice();
            $skuData['costPrice'] = $sku->getCostPrice();
            $skuData['originalPrice'] = $sku->getOriginalPrice();

            foreach ($sku->getAttributes() as $attribute) {
                $skuData['attributes'][] = [
                    'id' => $attribute->getId(),
                    'name' => $attribute->getName(),
                    'value' => $attribute->getValue(),
                ];
            }

            $skus[] = $skuData;
        }

        $attributes = [];
        foreach ($spu->getAttributes() as $attribute) {
            $attributes[] = [
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'value' => $attribute->getValue(),
            ];
        }

        $descriptionAttributes = [];
        foreach ($spu->getAttributes() as $attribute) {
            $descriptionAttributes[] = [
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'value' => $attribute->getValue(),
            ];
        }

        $categories = [];
        foreach ($spu->getCategories() as $category) {
            $categories[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'valid' => $category->isEnabled(),
            ];
        }

        return [
            'id' => $spu->getId(),
            'gtin' => $spu->getGtin(),
            'title' => $spu->getTitle(),
            'subtitle' => $spu->getSubtitle(),
            'type' => $spu->getType(),
            'brand' => ($brand = $spu->getBrand()) !== null ? [
                'id' => $brand->getId(),
                'name' => $brand->getName(),
            ] : null,
            'mainPic' => $spu->getMainPic(),
            'mainThumb' => $this->formatterService->getSpuMainThumb($spu),
            'images' => $spu->getImages(),
            'content' => $spu->getContent(),
            'valid' => $spu->isValid(),
            'createTime' => $spu->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $spu->getUpdateTime()?->format('Y-m-d H:i:s'),
            'skus' => $skus,
            'attributes' => $attributes,
            'descriptionAttributes' => $descriptionAttributes,
            'categories' => $categories,
        ];
    }
}
