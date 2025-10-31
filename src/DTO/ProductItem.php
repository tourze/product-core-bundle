<?php

namespace Tourze\ProductCoreBundle\DTO;

final class ProductItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?float $price,
        public readonly ?float $originalPrice,
        public readonly string $thumbnail,
        public readonly int $sales,
        public readonly int $stock,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'originalPrice' => $this->originalPrice,
            'thumbnail' => $this->thumbnail,
            'sales' => $this->sales,
            'stock' => $this->stock,
        ];
    }
}
