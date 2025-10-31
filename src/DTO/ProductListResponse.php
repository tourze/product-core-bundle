<?php

namespace Tourze\ProductCoreBundle\DTO;

final class ProductListResponse
{
    /**
     * @param ProductItem[] $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $page,
        public readonly int $limit,
        public readonly int $totalPages,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'items' => array_map(fn (ProductItem $item) => $item->toArray(), $this->items),
            'pagination' => [
                'total' => $this->total,
                'page' => $this->page,
                'limit' => $this->limit,
                'totalPages' => $this->totalPages,
            ],
        ];
    }
}
