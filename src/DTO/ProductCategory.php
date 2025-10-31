<?php

namespace Tourze\ProductCoreBundle\DTO;

final class ProductCategory
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly bool $valid,
    ) {
    }

    /**
     * 获取分类ID - 兼容性方法
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'valid' => $this->valid,
        ];
    }
}
