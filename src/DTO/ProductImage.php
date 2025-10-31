<?php

namespace Tourze\ProductCoreBundle\DTO;

final class ProductImage
{
    public function __construct(
        public readonly string $url,
        public readonly int $sort,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'sort' => $this->sort,
        ];
    }
}
