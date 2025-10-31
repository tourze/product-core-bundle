<?php

namespace Tourze\ProductCoreBundle\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class BatchUpdateProductStatusRequest
{
    /**
     * @var array<int>
     */
    #[Assert\NotBlank(message: '商品ID列表不能为空')]
    #[Assert\Type(type: 'array', message: '商品ID列表必须为数组')]
    #[Assert\Count(min: 1, max: 1000, minMessage: '至少需要1个商品ID', maxMessage: '单次批量操作不能超过1000个商品')]
    #[Assert\All(constraints: [
        new Assert\Type(type: 'int', message: '商品ID必须为整数'),
        new Assert\Positive(message: '商品ID必须为正整数'),
    ])]
    public array $productIds = [];

    #[Assert\NotNull(message: '状态值不能为空')]
    #[Assert\Choice(choices: [0, 1], message: '状态值只能是0(下架)或1(上架)')]
    public int $status = 0;

    /**
     * @param array<int> $productIds
     */
    public function __construct(array $productIds = [], int $status = 0)
    {
        $this->productIds = array_values(array_unique($productIds));
        $this->status = $status;
    }

    /**
     * @return array<int>
     */
    public function getUniqueProductIds(): array
    {
        return array_values(array_unique($this->productIds));
    }

    public function isStatusValid(): bool
    {
        return in_array($this->status, [0, 1], true);
    }

    public function getStatusDescription(): string
    {
        return 1 === $this->status ? '上架' : '下架';
    }
}
