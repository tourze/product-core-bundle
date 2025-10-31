<?php

namespace Tourze\ProductCoreBundle\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductListFilterRequest
{
    #[Assert\PositiveOrZero(message: '最低价格不能小于0')]
    public ?float $minPrice = null;

    #[Assert\PositiveOrZero(message: '最高价格不能小于0')]
    #[Assert\GreaterThan(propertyPath: 'minPrice', message: '最高价格必须大于最低价格')]
    public ?float $maxPrice = null;

    #[Assert\Choice(choices: ['sales_desc', 'sales_asc'], message: '销量排序只能是 sales_desc 或 sales_asc')]
    public ?string $salesSort = null;

    #[Assert\Choice(choices: ['price_desc', 'price_asc'], message: '价格排序只能是 price_desc 或 price_asc')]
    public ?string $priceSort = null;

    #[Assert\Positive(message: '分类ID必须大于0')]
    public ?int $categoryId = null;

    #[Assert\Positive(message: '页码必须大于0')]
    public int $page = 1;

    #[Assert\Range(min: 1, max: 100, notInRangeMessage: '每页数量必须在1到100之间')]
    public int $limit = 20;
}
