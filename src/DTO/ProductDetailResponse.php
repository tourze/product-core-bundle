<?php

namespace Tourze\ProductCoreBundle\DTO;

final class ProductDetailResponse
{
    /**
     * @param array<ProductSkuDetail> $skus
     * @param array<ProductAttribute> $attributes
     * @param array<ProductAttribute> $descriptionAttributes
     * @param array<ProductCategory> $categories
     * @param array<ProductImage> $images
     * @param array<mixed>|null $salePrices
     * @param ProductBrand|null $brand
     */
    public function __construct(
        public readonly int $id,
        public readonly ?string $gtin,
        public readonly string $title,
        public readonly ?string $subtitle,
        public readonly ?string $type,
        public readonly ?ProductBrand $brand,
        public readonly ?string $mainPic,
        public readonly string $mainThumb,
        public readonly array $images,
        public readonly ?string $content,
        public readonly bool $valid,
        public readonly bool $audited,
        public readonly ?string $autoReleaseTime,
        public readonly ?string $createTime,
        public readonly ?string $updateTime,
        public readonly array $skus,
        public readonly array $attributes,
        public readonly array $descriptionAttributes,
        public readonly array $categories,
        public readonly ?array $salePrices,
        public readonly ?string $displaySalePrice,
        public readonly ?string $displayTaxPrice,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'gtin' => $this->gtin,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'type' => $this->type,
            'brand' => $this->brand?->toArray(),
            'mainPic' => $this->mainPic,
            'mainThumb' => $this->mainThumb,
            'images' => array_map(fn (ProductImage $image) => $image->toArray(), $this->images),
            'content' => $this->content,
            'valid' => $this->valid,
            'audited' => $this->audited,
            'autoReleaseTime' => $this->autoReleaseTime,
            'createTime' => $this->createTime,
            'updateTime' => $this->updateTime,
            'skus' => array_map(fn (ProductSkuDetail $sku) => $sku->toArray(), $this->skus),
            'attributes' => array_map(fn (ProductAttribute $attr) => $attr->toArray(), $this->attributes),
            'descriptionAttributes' => array_map(fn (ProductAttribute $attr) => $attr->toArray(), $this->descriptionAttributes),
            'categories' => array_map(fn (ProductCategory $cat) => $cat->toArray(), $this->categories),
            'salePrices' => $this->salePrices,
            'displaySalePrice' => $this->displaySalePrice,
            'displayTaxPrice' => $this->displayTaxPrice,
        ];
    }
}
