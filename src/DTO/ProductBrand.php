<?php

namespace Tourze\ProductCoreBundle\DTO;

final class ProductBrand
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
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
        ];
    }
}
