<?php

namespace Tourze\ProductCoreBundle\DTO;

final class ProductPrice
{
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly ?string $price,
        public readonly ?string $currency,
        public readonly ?float $taxRate,
        public readonly ?int $priority,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'price' => $this->price,
            'currency' => $this->currency,
            'taxRate' => $this->taxRate,
            'priority' => $this->priority,
        ];
    }
}
