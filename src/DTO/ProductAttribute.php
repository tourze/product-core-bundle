<?php

namespace Tourze\ProductCoreBundle\DTO;

final class ProductAttribute
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $value,
        public readonly ?string $type = null,
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
            'value' => $this->value,
            'type' => $this->type,
        ];
    }
}
