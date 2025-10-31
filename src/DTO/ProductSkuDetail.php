<?php

namespace Tourze\ProductCoreBundle\DTO;

final class ProductSkuDetail
{
    /**
     * @param array<ProductPrice> $prices
     * @param array<ProductAttribute> $attributes
     */
    public function __construct(
        public readonly int $id,
        public readonly ?string $gtin,
        public readonly string $title,
        public readonly bool $valid,
        public readonly int $stock,
        public readonly int $salesReal,
        public readonly int $salesVirtual,
        public readonly int $totalSales,
        public readonly array $prices,
        public readonly array $attributes,
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
            'valid' => $this->valid,
            'stock' => $this->stock,
            'salesReal' => $this->salesReal,
            'salesVirtual' => $this->salesVirtual,
            'totalSales' => $this->totalSales,
            'prices' => array_map(fn (ProductPrice $price) => $price->toArray(), $this->prices),
            'attributes' => array_map(fn (ProductAttribute $attr) => $attr->toArray(), $this->attributes),
        ];
    }
}
