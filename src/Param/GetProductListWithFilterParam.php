<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPCPaginatorBundle\Param\PaginatorParamInterface;

readonly class GetProductListWithFilterParam implements PaginatorParamInterface
{
    public function __construct(
        #[MethodParam(description: '最低价格')]
        public ?float $minPrice = null,
        #[MethodParam(description: '关键词')]
        public ?string $keyword = null,
        #[MethodParam(description: '最高价格')]
        public ?float $maxPrice = null,
        #[MethodParam(description: '销量排序 (sales_desc, sales_asc)')]
        public ?string $salesSort = null,
        #[MethodParam(description: '价格排序 (price_desc, price_asc)')]
        public ?string $priceSort = null,
        #[MethodParam(description: '分类ID')]
        public ?int $categoryId = null,
        #[MethodParam(description: '标签ID')]
        public ?int $tagId = null,
        #[MethodParam(description: '是否包含SKU数据')]
        public bool $includeSku = true,
        #[MethodParam(description: '每页条数')]
        public int $pageSize = 10,
        #[MethodParam(description: '当前页数')]
        public int $currentPage = 1,
        #[MethodParam(description: '上一次拉取时，最后一条数据的主键ID')]
        public ?int $lastId = null,
    ) {
    }
}
